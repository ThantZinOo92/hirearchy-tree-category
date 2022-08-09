@extends('app')

@section('title', 'Create Category')

@push('after-styles')
@endpush

@section('content')

    <div class="card">
        <div class="card-header bg-info">
            Category Management
            <a class="btn btn-danger float-right" href="{{ url('/') }}" role="button">Home</a>
        </div>
        <div class="card-body">
            <form id="category_form">
                <div id='alert_div'>
                </div>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" value="{{ $type =='update' ? $category->name : '' }}" name="name" placeholder="category name..." required>
                </div>
                @php   
                    
                @endphp
                @if($type == 'create')
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="true" name="is_main_category">
                            <label class="form-check-label" for="is_main_category">
                            Main Category 
                            </label>
                        </div>
                    </div>
                @endif

                <div class="form-group" id="parentCategories">
                </div>

                <div class="form-group">
                    <h4>Add Language</h4>
                    <div class ="p-1">
                        <button type="button" class="btn btn-danger" id="add_language">+</button>
                    </div>
                    <div class="form-group" id="new_languages">
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer bg-info">
            Category Management
            @if($type == 'update')
                <button type="button" category_id = "{{ $category->id }}" class="btn btn-danger float-right" id="updateBtn">Update</button>
            @else
                <button type="button" class="btn btn-danger float-right" id="createBtn">Create</button>
            @endif
        </div>
    </div>
@endsection

@push('after-scripts')
<script type="text/javascript">
    $(document).ready(function () {

    getCategories();
    let type = "{{ $type }}";
    let oldCategory = <?php echo $type == 'update' ?  json_encode($category) : json_encode([]); ?>;
    if(type == 'update' && oldCategory.parent_id == 0) $('#parentCategories').html("");
    if(type == 'update'){
        $.each(oldCategory.languages, function(key, value){
            languageHtml(value);
        });
    }

        
    function getCategories(){
        $.ajax({
                url : "{{ url('api/categories') }}",
                method : "GET",
                dataType : "json",
                success : function (data){
                let appenDHtml= "";
                if((type == 'update' && oldCategory.parent_id > 0) || type == 'create'){
                        appenDHtml = "<h4>Choose Your Parent Categories</h4>";
                    
                    $.each(data, function(key,val){
                        let checked = (oldCategory.parent_id > 0 && oldCategory.parent_id == val.id) ? 'checked' : '';
                        if(type == 'update' && oldCategory.id == val.id){
                            console.log('no add.');
                        }
                        else
                        {
                            appenDHtml += `<div class="form-check">
                                            <input class="form-check-input" type="radio" value="` + val.id + `" name="parent_id" `+ checked +`>
                                            <label class="form-check-label" for="parent_id">
                                            ` + val.name + ` 
                                            </label>
                                        </div>`;
                        }

                        
                    });
                }
                $('#parentCategories').html(appenDHtml);
                },
                error : function (error){
                    console.log(error);
                }
            });
    }

    function languageHtml(oldData = null){
        let name = oldData != null ? oldData.name : '';
        let translation = oldData != null ? oldData.translation : '';
        let appendHtml = `<div class="row border-bottom border-dark p-1 m-1 new_languages">
                                <div class="col-10">
                                <label for="name">Language Name</label>
                                <input type="text" class="form-control" name="language_name[]" value="`+ name +`" placeholder="langugae name...">
                                <label for="name">Translation</label>
                                <input type="text" class="form-control" name="translation[]" value="`+ translation +`" placeholder="translation for category name...">
                                </div>
                            
                                <div class="col-2">
                                    <button type="button" class="btn btn-danger">x</button>
                                </div>
                            </div>`;
        $('#new_languages').append(appendHtml);
    }

    $("#add_language" ).click(function() {
        languageHtml();
    });

    $("#new_languages").on('click', 'button', function() {
        $(this).closest('div.new_languages').remove();
    });

    $("input[name='is_main_category']").change(function() {
        if(this.checked) {
            $('#parentCategories').html("");
        }
        else{
            getCategories();
        }
    });
    });
      
    $("#createBtn").click(function(){
        let url = "{{ url('api/categories') }}";
        let method = 'POST';
        submitForm(url,method);
    });
    $("#updateBtn").click(function(){
        let category_id = $(this).attr('category_id')
        let url = "{{ url('api/categories')}}" + "/" +category_id;
        let method = 'PUT';
        submitForm(url,method);
    });
    function submitForm(url,method){
        let formData = $('#category_form').serialize();
        $.ajax({
            type: method,
            url: url,
            data: formData,
            dataType: "json",
            encode: true,
            success : function (data){
                        let showSuccess = `<div class="alert alert-success alert-dismissible fade show" role="alert">
                                             `+ data.message +`
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            </div>`;
                       $('#alert_div').html(showSuccess);
                       window.location = "/";
                       
                  },
            error : function (error){
                    let showErrors = "";
                    $.each(error.responseJSON, function(key,val){
                        showErrors += `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                             `+ val+`
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            </div>`;
                    });

                    $('#alert_div').html(showErrors);

                   
                  }
        })
    }
</script>
@endpush