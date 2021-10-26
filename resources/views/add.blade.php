<html>

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Access-Control-Allow-Origin" content="*" />
</head>
<style>
    
</style>

<body>
    <div class="container">
        <div id="app" class="justify-content-center align-items-center">
            <div class="row" style="margin-bottom: 60px;">
                <div class="col">
                    <h2 id="title" class="text-center font-weight-bold" style="margin-bottom:20px;">新增文章</h2>
                    <div class="card">
                        <div class="card-body">
                            <form id="save" action="/addArticles" method="POST">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="InputTitle">標題</label>
                                    <input type="text" class="form-control" id="InputTitle" name="InputTitle" value=" ">
                                </div>
                                <div class="form-group">
                                    <label for="InputContent">內容</label>
                                    <textarea  type="text" class="form-control" id="InputContent" name="InputContent" maxlength="500"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="createDate">日期</label>
                                    <input type="text" class="form-control" id="createDate" name="createDate" data-provide="datepicker">
                                </div>
                                <div class="form-group form-check">
                                    <label for="Author">作者</label>
                                    <input type="text" class="form-control" id="Author" name="Author" value=" ">
                                </div>
                                <div class="form-group">
                                    <input type="button" class="btn btn-primary" value="儲存" onClick="addArticles()">
                                </div>
                            <form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{mix('js/app.js')}}"></script>
    <link rel="stylesheet" href="./css/datepicker3.css"/>
    <script src="https://cdn.jsdelivr.net/bootstrap.datepicker-fork/1.3.0/js/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="./js/bootstrap-datetimepicker.zh-TW.js" charset="UTF-8"></script>
    <script>
        $("input[name='onlineDate']").datepicker({
            uiLibrary: 'bootstrap4',
            format: "yyyy-mm-dd",
            language:"zh-TW",
            weekStart: 1,
            daysOfWeekHighlighted: "6,0",
            autoclose: true,
            todayHighlight: true,
        });

        function addArticles(){
            let InputTitle = $('#InputTitle').val();
            let InputContent = $('#InputContent').val();
            let message = '';

            if(InputTitle == '' || InputTitle == null){
                message += '請輸入標題' + '<br/>';
            }

            if(InputContent == '' || InputContent == null){
                message += '請輸入內容' + '<br/>';
            }

            if(message != ''){
                alert(message);
            }else{
                $('#save').submit();
            }
        }
    </script>
</body>

</html>