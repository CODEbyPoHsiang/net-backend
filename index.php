<?php
 header("Content-type: text/html; charset=utf-8");
 
 function validate($user, $pass)
 {
     $users = ['admin'=>'admin'];
     if (isset($users[$user]) && $users[$user] === $pass) {
         return true ;
     } else {
         return false;
     }
 }

if (!validate(@$_SERVER['PHP_AUTH_USER'], @$_SERVER['PHP_AUTH_PW'])) {
    http_response_code(401);
    header('WWW-Authenticate:Basic realm="My website"'); //對話方塊顯示 http://127.0.0.3 請求使用者名稱和密碼。資訊為：My website
    echo '需要使用者名稱和密碼才能繼續訪問'; //取消時瀏覽器輸出
    exit;
} else {
    if (!($_SERVER['PHP_AUTH_USER']=="admin"&&$_SERVER['PHP_AUTH_PW']=="admin")) {
        echo "Username or Password not correct";
        $_SERVER['PHP_AUTH_USER']="";
        $_SERVER['PHP_AUTH_PW']="";
        //echo "<pre>";
        //print_r($_SERVER);
        exit;
    }
}
 
   ?>

<html>

<head>
    <title>Neithent 後台</title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.10.2/css/all.css"
        integrity="sha384-rtJEYb85SiYWgfpCr0jn174XgJTn4rptSOQsMroFBPQSGLdOC5IbubP6lJ35qoM9" crossorigin="anonymous" />
    <!-- boostrap -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://bootswatch.com/4/darkly/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- datepicker css  -->
    <link rel="stylesheet" href="./css/datetimepicker.css" />

    <!-- 使用sweetalert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
    <script src="https://cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.js"></script>

    <style>
    body {
        margin: 0;
        padding: 0;
        /* background-color: #f1f1f1; */
    }

    .box {
        width: 1270px;
        padding: 20px;
        /* background-color: #fff; */
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-top: 100px;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand">Neithent 後台</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01"
            aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>


    </nav>

    <div class="container box">

        <br />
        <div align="right">
            <button type="button" id="modal_button" class="btn btn-info">新增</button>

        </div>
        <br />
        <div id="result" class="table-responsive">

        </div>
    </div>
</body>

</html>
<!-- 編輯跟新增共用modal -->
<div id="postModal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create New Records</h4>
            </div>
            <from>
                <div class="modal-body">
                    <label>類型</label>
                    <select name="category" id="category" class="form-control">
                        <option selected disabled value="" required="required">請選擇文章類型</option>
                        </option>
                        <option value="event">EVENT</option>
                        </option>
                        <option value="news">NEWS</option>
                    </select>
                    <br />
                    <label>主題</label>
                    <input type="text" name="subject" id="subject" class="form-control" placeholder="請輸入主題" required />
                    <br />
                    <label for="datetimepicker">公告日期</label>
                    <input type="text" name="datetime" id="datetimepicker" class="form-control datetimepicker"
                        placeholder="請選擇日期" />
                    <br />
                    <label>內容</label>
                    <input type="text" name="content" id="content" class="form-control" placeholder="請輸入內容" />
                    <br />

                    <label>是否顯示</label>
                    <select name="is_show" id="is_show" class="form-control">
                        <option selected="selected" disabled value="">請選擇文章狀態</option>
                        </option>
                        <option value="Yes">顯示</option>
                        </option>
                        <option value="No">隱藏</option>
                        </option>
                    </select>

                    <br />
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="customer_id" id="customer_id" />
                    <input type="submit" name="action" id="action" class="btn btn-success" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </from>
        </div>
    </div>
</div>

<!-- 詳細內容 modal -->
<div id="infoModal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">PostINFO</h4>
            </div>
            <from>
                <div class="modal-body">
                <div id="post_info" class="table-responsive">

</div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="customer_id" id="customer_id" />
                    <button type="button" class="btn btn-secondary disabled" data-dismiss="modal">Close</button>
                </div>
            </from>
        </div>
    </div>
</div>
<!-- WYSWYG Editor -->
<script src="https://cdn.ckeditor.com/4.13.0/standard/ckeditor.js"></script>
<script src="./js/plugins/moment.min.js"></script>
<script src="./js/plugins/bootstrap-datetimepicker.js"></script>
<script>
//日期選擇器
// datetimepicker init
$('.datetimepicker').datetimepicker({
    icons: {
        time: 'fa fa-clock',
        date: 'fa fa-calendar',
        up: 'fa fa-chevron-up',
        down: 'fa fa-chevron-down',
        previous: 'fa fa-chevron-left',
        next: 'fa fa-chevron-right',
        today: 'fa fa-screenshot',
        clear: 'fa fa-trash',
        close: 'fa fa-remove',
    },
    format: "YYYY/ MM / DD"
});
//文字編輯器 content 改成編輯器
CKEDITOR.replace('content');



$(document).ready(function() {
    fetchUser();

    function fetchUser() {
        var action = "Load";
        console.log(action);

        $.ajax({
            url: "action.php",
            method: "POST",
            data: {
                action: action
            },
            success: function(data) {
                $('#result').html(data);
            }
        });
    }

    $('#modal_button').click(function() {
        $('#postModal').modal('show');
        $('#category').val('');
        $('#subject').val('');
        // $('#content').val('');
        CKEDITOR.instances.content.setData('');
        $('#datetimepicker').val('');
        $("#is_show").val('');
        $('.modal-title').text("新增文章");
        $('#action').val('Create');
    });

    $('#action').click(function() {
        var category = $('#category').val();
        var subject = $('#subject').val();
        var datetime = $('#datetimepicker').val();
        var content = CKEDITOR.instances.content.getData();
        var is_show = $("#is_show").val();
        var id = $('#customer_id').val();
        var action = $('#action').val();


        if (category == '') {
            alert(
                "請選擇【文章類型】"
            );
            return false;
        }
        if (subject == '') {
            alert(
                "請填入【文章主題】"
            );
            return false;
        }
        if (content == '') {
            alert(
                "請填入【文章內容】"
            );
            return false;
        }
        if (datetime == '') {
            alert(
                "請選擇【公告日期】"
            );
            return false;
        } else {
            $.ajax({
                url: "action.php",
                method: "POST",
                data: {
                    category: category,
                    subject: subject,
                    content: content,
                    datetime: datetime,
                    is_show: is_show,
                    id: id,
                    action: action
                },
                success: function(data) {
                    alert(
                        data);
                    $('#postModal').modal(
                        'hide');
                    fetchUser
                        ();
                }
            });

        }


        // if (category != '' && subject != '' && content != '' && datetime != '') {
        //     $.ajax({
        //         url: "action.php",
        //         method: "POST",
        //         data: {
        //             category: category,
        //             subject: subject,
        //             content: content,
        //             datetime: datetime,
        //             is_show: is_show,
        //             id: id,
        //             action: action
        //         },
        //         success: function(data) {
        //             alert(
        //                 data);
        //             $('#postModal').modal(
        //                 'hide');
        //             fetchUser
        //                 ();
        //         }
        //     });
        // } else {
        //     alert(
        //         "請檢查欄位是否已填"
        //     );
        // }
    });

    $(document).on('click', '.update', function() {
        var id = $(this).attr(
            "id"
        );

        var action = "Select";
        $.ajax({
            url: "action.php",
            method: "POST",
            data: {
                id: id,
                action: action
            },
            dataType: "json",
            success: function(data) {
                $('#postModal').modal('show');
                $('.modal-title').text(
                    "Update Records"
                );
                $('#action').val("Update");
                $('#customer_id').val(
                    id
                );
                $('#category').val(data
                    .category);
                $('#subject').val(data
                    .subject);
                $('#datetimepicker').val(data
                    .datetime);
                $("#is_show").val(data
                    .is_show);
                $('#content').val(data
                    .content);
                    CKEDITOR.instances.content.setData(data.content);
                    CKEDITOR.instances.content.getData();
            }
        });
    });

    //點選詳細按鈕
    $(document).on('click', '.info', function() {
        var id = $(this).attr(
            "id"
        );

        var action = "Info";
        $.ajax({
            url: "action.php",
            method: "POST",
            data: {
                id: id,
                action: action
            },
            success: function(data) {
                $('#infoModal').modal('show');
                $('.modal-title').text(
                    "Post Info"
                );
                $('#post_info').html(data);
               
            }
        });
    });

    $(document).on('click', '.delete', function() {
        var id = $(this).attr(
            "id"
        );
        if (confirm("確定要刪除此筆資料嗎?")) {
            var action = "Delete";
            $.ajax({
                url: "action.php",
                method: "POST",
                data: {
                    id: id,
                    action: action
                },
                success: function(data) {
                    fetchUser
                        ();
                    alert(
                        data);
                }
            })
        } else {
            return false;
        }
    });
});
</script>