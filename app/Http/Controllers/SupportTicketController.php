<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ticket;
use App\User;
use Auth;
use App\TicketReply;
use App\Mail\SupportMailManager;
use Illuminate\Support\Facades\Validator;
use Mail;
use function GuzzleHttp\Psr7\str;

class SupportTicketController extends Controller
{
    protected function validator(array $data, $type=null)
    {
        if ($type == 'reply'){
            return Validator::make($data, [
                "reply"  => "required|string",
                "ticket_id"  => "required|string",
                'attachments'  =>'nullable|array|min:1',
                'attachments.*' => 'nullable|mimes:jpg,png,jpeg,svg,pdf,docx|max:2000'
            ],[
                'reply.required' => 'The field reply is required.\n',
                'reply.string' => 'The field reply must be string.\n',
                'ticket_id.required' => 'The ticket is required.\n',
                'ticket_id.string' => 'The ticket must be string.\n',
                'attachments.*.mimes' => 'The attachments extension must include: jpg,png,jpeg,svg,pdf,docx.\n',
                'attachments.*.max' => 'The attachments size must be less than 2MB.\n'
            ]);
        }
        return Validator::make($data, [
            "subject"  => "required|string|max:255",
            "details"  => "required|string|max:500",
            'attachments'  =>'nullable|array|min:1',
            'attachments.*' => 'nullable|mimes:jpg,png,jpeg,svg,pdf,docx|max:2000'
        ],[
            'subject.required' => 'Subject is required.\n',
            'subject.string' => 'Subject must be string.\n',
            'subject.max' => 'Subject must not exceed 255 characters.\n',
            'details.required' => 'Details is required.\n',
            'details.string' => 'Details must be string.\n',
            'details.max' => 'Details must not exceed 500 characters.\n',
            'attachments.*.mimes' => 'The attachments extension must include: jpg,png,jpeg,svg,pdf,docx.\n',
            'attachments.*.max' => 'The attachments size must be less than 2MB.\n'
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tickets = Ticket::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->paginate(9);
        return view('frontend.support_ticket.index', compact('tickets'));
    }

    public function admin_index(Request $request)
    {
        $sort_search =null;
        $tickets = Ticket::orderBy('created_at', 'desc');
        if ($request->has('search')){
            $sort_search = $request->search;
            $tickets = $tickets->where('code', 'like', '%'.$sort_search.'%');
        }
        $tickets = $tickets->paginate(15);
        return view('support_tickets.index', compact('tickets', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $validation = $this->validator($request->all());
       if($validation->fails()) {
           $error = '';
           foreach ($validation->errors()->getMessages() as $key =>$err) {
               $error .= $err[0];
           }
           flash()->error($error);
           return back();
       }

        $ticket = new Ticket;
        $ticket->code = max(100000, (Ticket::latest()->first() != null ? Ticket::latest()->first()->code + 1 : 0)).date('s');
        $ticket->user_id = Auth::user()->id;
        $ticket->subject = $request->subject;
        $ticket->details = $request->details;

        $files = array();

        if($request->hasFile('attachments')){
            foreach ($request->attachments as $key => $attachment) {
                $item['name'] = $attachment->getClientOriginalName();
                $item['path'] = $attachment->store('uploads/support_tickets/');
                array_push($files, $item);
            }
            $ticket->files = json_encode($files);
        }

        if($ticket->save()){
            $this->send_support_mail_to_admin($ticket);
            flash('Ticket has been sent successfully')->success();
            return redirect()->route('support_ticket.index');
        }
        else{
            flash('Something went wrong')->error();
        }


    }

    public function send_support_mail_to_admin($ticket){
        $array['view'] = 'emails.support';
        $array['subject'] = 'Support ticket Code is:- '.$ticket->code;
        $array['from'] = env('MAIL_USERNAME');
        $array['content'] = 'Hi. A ticket has been created. Please check the ticket.';
        $array['link'] = route('support_ticket.admin_show', encrypt($ticket->id));
        $array['sender'] = $ticket->user->name;
        $array['details'] = $ticket->details;

        // dd($array);
        // dd(User::where('user_type', 'admin')->first()->email);
        try {
            Mail::to(User::where('user_type', 'admin')->first()->email)->queue(new SupportMailManager($array));
        } catch (\Exception $e) {
            // dd($e->getMessage());
        }
    }

    public function send_support_reply_email_to_user($ticket, $tkt_reply){
        $array['view'] = 'emails.support';
        $array['subject'] = 'Support ticket Code is:- '.$ticket->code;
        $array['from'] = env('MAIL_USERNAME');
        $array['content'] = 'Hi. A ticket has been created. Please check the ticket.';
        $array['link'] = route('support_ticket.show', encrypt($ticket->id));
        $array['sender'] = $tkt_reply->user->name;
        $array['details'] = $tkt_reply->reply;

        // dd($array);
        // dd(User::where('user_type', 'admin')->first()->email);
        try {
            Mail::to($ticket->user->email)->queue(new SupportMailManager($array));
        } catch (\Exception $e) {
            //dd($e->getMessage());
        }
    }

    public function admin_store(Request $request)
    {
        $validation = $this->validator($request->all(),'reply');
        if($validation->fails()) {
            $error = '';
            foreach ($validation->errors()->getMessages() as $key =>$err) {
                $error .= $err[0];
            }
            flash()->error($error);
            return back();
        }
        $ticket_reply = new TicketReply;
        $ticket_reply->ticket_id = $request->ticket_id;
        $ticket_reply->user_id = Auth::user()->id;
        $ticket_reply->reply = $request->reply;

        $files = array();

        if($request->hasFile('attachments')){
            foreach ($request->attachments as $key => $attachment) {
                $item['name'] = $attachment->getClientOriginalName();
                $item['path'] = $attachment->store('uploads/support_tickets/');
                array_push($files, $item);
            }
            $ticket_reply->files = json_encode($files);
        }

        $ticket_reply->ticket->client_viewed = 0;
        $ticket_reply->ticket->status = $request->status;
        $ticket_reply->ticket->save();


        if($ticket_reply->save()){
            flash('Reply has been sent successfully')->success();
            $this->send_support_reply_email_to_user($ticket_reply->ticket, $ticket_reply);
            return back();
        }
        else{
            flash('Something went wrong')->error();
        }
    }

    public function seller_store(Request $request)
    {
        $validation = $this->validator($request->all(),'reply');
        if($validation->fails()) {
            $error = '';
            foreach ($validation->errors()->getMessages() as $key =>$err) {
                $error .= $err[0];
            }
            flash()->error($error);
            return back();
        }
        $ticket_reply = new TicketReply;
        $ticket_reply->ticket_id = $request->ticket_id;
        $ticket_reply->user_id = $request->user_id;
        $ticket_reply->reply = $request->reply;

        $files = array();

        if($request->hasFile('attachments')){
            foreach ($request->attachments as $key => $attachment) {
                $item['name'] = $attachment->getClientOriginalName();
                $item['path'] = $attachment->store('uploads/support_tickets/');
                array_push($files, $item);
            }
            $ticket_reply->files = json_encode($files);
        }

        $ticket_reply->ticket->viewed = 0;
        $ticket_reply->ticket->status = 'pending';
        $ticket_reply->ticket->save();
        if($ticket_reply->save()){

            flash('Reply has been sent successfully')->success();
            return back();
        }
        else{
            flash('Something went wrong')->error();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ticket = Ticket::findOrFail(decrypt($id));
        $ticket->client_viewed = 1;
        $ticket->save();
        $ticket_replies = $ticket->ticketreplies;
        return view('frontend.support_ticket.show', compact('ticket','ticket_replies'));
    }

    public function admin_show($id)
    {
        $ticket = Ticket::findOrFail(decrypt($id));
        $ticket->viewed = 1;
        $ticket->save();
        return view('support_tickets.show', compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
