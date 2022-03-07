<style>
    .permissions {
        display: flex;
        justify-content: flex-start;
        flex-wrap: wrap;
    }

    .form-group .permission-id {
        border: 1px solid #c5c1c1;
        padding: 2px;
        margin: .5rem;
        display: block;
        width: auto;
        float: left;
        color: #6aafbf;
    }

    .select-mark {
        border: 2px solid #ffb9b9 !important;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">角色( {{$data['name']}} )权限编辑</h3>
                <div class="box-tools">
                    <div class="btn-group pull-right" style="margin-right: 5px">
                        <a href="/admin/auth-rewrite/role" class="btn btn-sm btn-default" title="列表"><i class="fa fa-list"></i><span class="hidden-xs">&nbsp;返回列表</span></a>
                    </div>
                </div>
            </div>
            <form id="edit-form" action="" method="post" accept-charset="UTF-8" class="form-horizontal" pjax-container="">
                <div class="box-body">
                    <div class="fields-group">
                        <div class="col-md-12">
                            @foreach($permissions_all as $group_mark=>$values)
                                <div class="form-group">
                                    <label for="course_day_number" class="col-sm-2 asterisk control-label">{{$group_mark ?: "默认分组"}}: <input type="checkbox" name="group_mark_checkbox" id="group_mark_checkbox" class="group_mark_checkbox"></label>
                                    <div class="col-sm-8 permissions">
                                        @foreach($values as $permissions)
                                            @if(in_array($permissions['id'], $permissions_use))
                                                <span class="permission-id select-mark" data-id="{{$permissions['id']}}">{{$permissions['name']}}</span>
                                            @else
                                                <span class="permission-id" data-id="{{$permissions['id']}}">{{$permissions['name']}}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <div class="btn-group pull-left">
                                <button type="button" class="btn btn-primary" id="btn_submit">提交</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('#edit-form').on('click', '.permission-id', function () {
            if ($(this).hasClass('select-mark')) {
                $(this).removeClass('select-mark')
            } else {
                $(this).addClass('select-mark')
            }
        });

        $('.group_mark_checkbox').click(function () {
            var is_check = $(this).prop("checked")
            if(is_check){
                $(this).parent().next().find('.permission-id').addClass('select-mark');
            }else{
                $(this).parent().next().find('.permission-id').removeClass('select-mark');
            }
        })


        var is_request = false;
        $("#btn_submit").on("click", function () {
            if (is_request) {
                return true;
            }
            var permissions_ids = [];
            $("#edit-form").find('.select-mark').each(function (i, v) {
                permissions_ids.push($(v).attr('data-id'))
            });
            var data = {
                role_id: "{{$data['id']}}",
                permissions_ids: permissions_ids.toString(),
                _token: $.admin.token
            };
            if (!data.permissions_ids) {
                toastr.error('权限必须选择');
                return;
            }

            $("#btn_submit").text('loading...');
            var pro = new Promise(function (resolve, reject) {
                is_request = true;
                $.post('/admin/auth-rewrite/role-permissions-save', data, function (res) {
                    is_request = false;
                    if (parseInt(res.code) === 1) {
                        resolve(res);
                    } else {
                        reject(res);
                    }
                }, 'json')
            });
            pro.then(function (res) {
                // console.log(res);
                toastr.success('操作成功');
                setTimeout(function () {
                    location.href = '/admin/auth-rewrite/role';
                }, 1000);

            }).catch(function (err) {
                console.log(err);
                $("#btn_submit").text('提交');
                toastr.error(err.msg, '', {timeOut: 0});
            });
        });
    })
</script>
