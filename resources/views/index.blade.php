@extends('app')

@section('title', 'Category Management')

@push('after-styles')
    <link href="{{ asset('css/treeview.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="card">
        <div class="card-header bg-info">
            Category Management
            <a class="btn btn-danger float-right" href="{{ url('categories/create') }}" role="button">Add New</a>
        </div>
        <div class="card-body">
            <div clas="row" id='alert_div'>
            </div>
            <div class="row border-bottom border-dark p-2">
                <label for="exampleFormControlSelect1">Search by parent</label>
                <div class="input-group">
                    <select class="custom-select" id="selectByParent">
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-outline-danger" type="button" id="searchBtn"><i class="bi bi-search"></i></button>
                    </div>
                </div>
            </div>
            <div class="pt-2">
                <ul id="tree1" class="row tree">
                </ul>
            </div>

        </div>
        <div class="card-footer bg-info">
            Category Management
        </div>
    </div>

@endsection

@push('after-scripts')
<script type="text/javascript">
    $(document).ready(function () {

        $.ajax({
                url : "/api/categories",
                method : "GET",
                dataType : "JSON",
                success : function (data){
                        appendList(data);
                        selectBox(data);
                },
                error : function (error){
                    console.log(error);
                }
        });


        $('#refreshBtn').click(function(){
            allRecords();
        });
        $("#searchBtn").click(function(){
            let parent_id = $('#selectByParent').find('option:selected').val();
            $.ajax({
                  url : "/api/categories_by_parent/" + parent_id,
                  method : "GET",
                  dataType : "JSON",
                  success : function (data){
                            appendList(data);
                  },
                  error : function (error){
                    console.log(error);
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
            });
        });

        function selectBox(data){
            let append_options = `<option selected value="0">Choose...</option>`;
            $.each(data,function(key,value){
                append_options += `<option value=` + value.id+ `>` + value.name + `</option>`
            })
            $("#selectByParent").html(append_options);
        }

        function appendList(data){
            let append_list = "";
            if(data.length){
                $.each(data, function(key,val){
                    append_list += `<li class='col-4'><a href="{{ url('categories/create/`+ val.id +`') }}" role="button">
                                    <i class="bi bi-pencil-fill">
                                    </i>
                                    </a>` + val.name;
                    if(val.childs.length){
                        append_list += appendChildList(val.childs)
                    }
                    append_list += `</li>`;


                 });
            }
            else{
                append_list ='<h5> No records... </h5>';
            }
          

            $("#tree1").html(append_list);
        }

        function appendChildList(childs){
            let childList = "<ul>";
            $.each(childs,function(key,val){
                    childList += "<li>" + val.name;
                    if(val.childs.length){
                        childList += appendChildList(val.childs);
                    }
                    childList += "</li>";
            })
            
            childList += "</ul>";
            return childList;
        }
    });
    
</script>
@endpush