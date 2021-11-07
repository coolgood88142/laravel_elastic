<html>

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container">
        <div id="app" class="justify-content-center align-items-center">
            <form id="save" action="/searchArticles" method="POST">
                {{ csrf_field() }}
                <div class="row" style="margin-bottom: 60px;">
                    <div class="col">
                        <h2 id="title" class="text-center font-weight-bold" style="margin-bottom:20px;">文章資訊</h2>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-11">
                                    <input type="text" class="form-control" id="keyword" name="keyword" value="{{ $keyword }}">
                                </div>
                                <div class="col-1">
                                    <input type="submit" class="btn btn-primary" id="search" name="search" value="查詢">
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-6">
                                                文章標題
                                            </div>
                                            <div class="col-6">
                                                建立日期
                                            </div>
                                        </div>
                                    </div>
                                    @foreach ($articles as $key => $article)
                                        <div class="row">
                                            <div class="col-6">
                                                <h3>{{ $article['_source']['title'] }}</h3>
                                            </div>
                                            <div class="col-6">
                                                <h3>{{ $article['_source']['createDate'] }}</h3>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="{{mix('js/app.js')}}"></script>
</body>

</html>