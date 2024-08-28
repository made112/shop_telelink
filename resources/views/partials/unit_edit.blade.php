
<div class="modal-header">
    <h5 class="modal-title strong-600 heading-5">{{translate('Update Unit')}}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<form class="form-horizontal" action="{{ route('your_unit.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id" value="{{ $unit->id }}">
    <div class="panel-body">
        <div class="form-group">
            <label class="col-sm-2 control-label" for="name">{{translate('Name')}}</label>
            <div class="col-sm-10">
                <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" value="{{ $unit->name }}" class="form-control" required>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="symbol">{{translate('Symbol')}}</label>
            <div class="col-sm-10">
                <input type="text" placeholder="{{translate('Symbol')}}" id="symbol" name="symbol" value="{{ $unit->symbol }}" class="form-control" required>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="code">{{translate('Code')}}</label>
            <div class="col-sm-10">
                <input type="text" placeholder="{{translate('Code')}}" id="code" name="code" value="{{ $unit->code }}" class="form-control" required>
            </div>
        </div>
    </div>
    <div class="panel-footer text-right">
        <button class="btn btn-purple" type="submit">{{translate('Save')}}</button>
    </div>
</form>
