@if(isset($city))
<div class="modal-header">
    <h5 class="modal-title strong-600 heading-5">{{translate('Update City')}}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<form class="form-horizontal" action="{{ route('cities.update', $city->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    {{ method_field('PUT') }}
    <input type="hidden" name="id" value="{{ $city->id }}">
    <div class="panel-body">
        <div class="form-group">
            <label class="col-sm-2 control-label" for="name">{{translate('Name')}}</label>
            <div class="col-sm-10">
                <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" value="{{ $city->name }}" class="form-control" required>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="nameAr">{{translate('Name Ar')}}</label>
            <div class="col-sm-10">
                <input type="text" placeholder="{{translate('Name Ar')}}" id="nameAr" name="nameAr" value="{{ $city->nameAr }}" class="form-control" required>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="type">{{translate('Type')}}</label>
            <div class="col-sm-10">
                <select type="text" placeholder="{{translate('Type')}}" id="type" name="type" class="form-control" required>
                    <option value="">{{translate('Select Type')}}</option>
                    <option value="jerusalem" @if($city->type == 'jerusalem') selected @endif>{{translate('Jerusalem')}}</option>
                    <option value="west_bank" @if($city->type == 'west_bank')selected @endif>{{translate('West Bank')}}</option>
                    <option value="occupied_interior" @if($city->type == 'occupied_interior')selected @endif>{{translate('Occupied Interior')}}</option>
                </select>
            </div>
        </div>
    </div>
    <div class="panel-footer text-right">
        <button class="btn btn-purple" type="submit">{{translate('Save')}}</button>
    </div>
</form>
@endif
