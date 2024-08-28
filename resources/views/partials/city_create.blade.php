
<div class="modal-header">
    <h5 class="modal-title strong-600 heading-5">{{translate('Add New City')}}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<form class="form-horizontal" action="{{ route('cities.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="panel-body">
        <div class="form-group">
            <label class="col-sm-2 control-label" for="name">{{translate('Name')}}</label>
            <div class="col-sm-10">
                <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" required>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="nameAr">{{translate('Name Ar')}}</label>
            <div class="col-sm-10">
                <input type="text" placeholder="{{translate('Name Ar')}}" id="nameAr" name="nameAr" class="form-control" required>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="type">{{translate('Type')}}</label>
            <div class="col-sm-10">
                <select type="text" placeholder="{{translate('Type')}}" id="type" name="type" class="form-control" required>
                    <option value="jerusalem">{{translate('Jerusalem')}}</option>
                    <option value="west_bank">{{translate('West Bank')}}</option>
                    <option value="occupied_interior">{{translate('Occupied Interior')}}</option>
                </select>
            </div>
        </div>
    </div>
    <div class="panel-footer text-right">
        <button class="btn btn-purple" type="submit">{{translate('Save')}}</button>
    </div>
</form>
