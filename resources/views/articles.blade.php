<html>

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container">
        <div id="app" class="justify-content-center align-items-center">
            <div class="row" style="margin-bottom: 60px;">
                <div class="col">
                    <h2 id="title" class="text-center font-weight-bold" style="margin-bottom:20px;">文章資訊</h2>
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
								<div class="card-header">
									文章列表
								</div>
								@foreach ($articles as $key => $article)
                                    <div class="row">
                                        <div class="col-8">
                                            <input type="button" class="list-group-item list-group-item-action" value="{{ $article->title }}" onclick="showArticleContent('{{ $article->id }}', null, null)" />
                                        </div>
                                        @if($article->author_id == $userId)
                                            <div class="col-4">
                                                <input type="button" class="btn btn-primary" name="read" value="刪除" onClick="deleteArticles(this, {{ $article->id }})"/>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                                <input type="button" class="btn btn-primary" value="新增文章" style="margin-top: 10px;" onclick="window.location.href='/add?userId={{ $userId }}'">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{mix('js/app.js')}}"></script>
    <script src="{{mix('js/notification.js')}}"></script>
    <link rel="stylesheet" href="./css/notification.css">
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="https://use.fontawesome.com/releases/v5.0.4/js/all.js"></script>
    <script>
        $(document).ready(function() {
            showNotification();
            Pusher.logToConsole = true;

            var pusher = new Pusher('408cd422417d5833d90d', {
                cluster: 'ap3',
                encrypted: true
            });

            var channel = pusher.subscribe('article-channel' + $('#userId').val());
            channel.bind('App\\Events\\SendMessage', function(data) {
                $.ajax({
                    url: "{{ route('getNotificationData') }}", 
                    type: 'POST',
                    data:{
                        'id' : $('#userId').val(),
                        'type' : data.userData.type,
                        '_token' : '{{csrf_token()}}'
                    },
                    success: function(result){
                        let date = new Date(data.broadcast.updated_at);
                        let diffDay = getDateDiff(date); 
                        let html = 
                            '<div name="notification" class="row">' +
                                '<div class="col-5">' +
                                    '<input type="button" class="list-group-item list-group-item-action text-danger" value="' + data.message + '"';
                                    if(data.userData.type == 'addChannel'){
                                        html += ' onclick="showChannelContent('+"'"+data.userData.id+"'"+', '+"'"+result.notificationId+"'"+')">';
                                    }else{
                                        html += ' onclick="showArticleContent('+"'"+data.userData.id+"'"+', '+"'"+result.notificationId+"'"+')">';
                                    }
                            html += '</div>';
                            html += '<div class="col-2">'
                                    if(diffDay < 7){
                                        if(diffDay == 0){
                                            
                                        }else{
                                            html += '已通知' + diffDay + '天';
                                        }
                                    }else{
                                      let month = date.getMonth();
                                      if(month < 10){
                                        month = "0" + month.toString();
                                      }
                                      html += date.getFullYear() + '-' + month + '-' + date.getDate();
                                    }
                            html += '</div>';
                            html += '<div class="col-4">';
                                    if(data.userData.type != 'deleteArticle'){
                                        html += '<input type="button" class="btn btn-primary" name="read" value="已閱讀"  onclick="readArticles(this, '+"'"+result.notificationId+"'"+')"';
                                        if(result.read_at != null){
                                            html += 'disabled';
                                        }
                                        html += '>';
                                    }

                           
                            html +='</div>'+
                            '</div>';
                        
                        let notification = $('#notificationRaw').html();
                        $('#notificationRaw').empty();
                        $('#notificationRaw').append(html);
                        $('#notificationRaw').append(notification);

                    },
                    error:function(xhr, status, error){
                        alert(xhr.statusText);
                    }
                })
            });
        });

        function showNotification(){
            $.ajax({
				url: "{{ route('showNotification') }}", 
				type: 'POST',
				data:{
                    'nowCount' : $("div[name='notification']").length,
					'count' : $('#notificationsCount').val(),
					'_token':'{{csrf_token()}}'
				},
				success: function(result){
                    // console.log(getDateDiff('2021-08-21'))
                    console.log(result);
                    $.each(result, function(index, value) {
                        let date = new Date(value.updated_at);

                        let html = 
                            '<div name="notification" class="row">' +
                                '<div class="col-5">' +
                                    '<input type="button" class="list-group-item list-group-item-action" value="' + value.data.title + '"';
                                    if(value.data.type == 'addChannel'){
                                        html += ' onclick="showChannelContent('+"'"+value.data.id+"'"+', '+"'"+value.id+"'"+')">';
                                    }else{
                                        html += ' onclick="showArticleContent('+"'"+value.data.id+"'"+', '+"'"+value.id+"'"+')">';
                                    }
                            html += '</div>';
                            html += '<div class="col-2">';
                            html +=  getDateDiff(date);
                            html += '</div>';
                            html += '<div class="col-4">';

                                if(value.data.type != 'deleteArticle'){
                                    html += '<input type="button" class="btn btn-primary" name="read" value="已閱讀"  onclick="readArticles(this, '+"'"+value.id+"'"+')"';
                                    if(value.read_at != null){
                                        html += 'disabled';
                                    }
                                    html += '>';
                                }

                            html +='</div>';

                            html += '</div>';
                        
                        $('#notificationRaw').append(html);
                    });
				},
				error:function(xhr, status, error){
					alert(xhr.statusText);
				}
			});
        }

        function showArticleContent(id, notificationId){
            let userId = $('#userId').val();
            let url = "{{ route('showArticleContent') }}" + '?id='+ id +'&userId='+ userId +'&isAdd=N';
            
            if(notificationId != null){
                url = url + '&notificationId=' + notificationId
            }

            window.location.href = url;
        }

        function showChannelContent(id, notificationId){
            let userId = $('#userId').val();
            let url = "{{ route('showChannelContent') }}" + '?channelsId='+ id +'&userId='+ userId;
            
            if(notificationId != null){
                url = url + '&notificationId=' + notificationId
            }

            window.location.href = url;
        }

		function readArticles(el, id){
			$.ajax({
				url: "{{ route('readArticles') }}", 
				type: 'POST',
				data:{
					"id" : id,
                    "userId" : $('#userId').val(),
					'_token':'{{csrf_token()}}'
				},
				success: function(result){
                    console.log(id);
                    //點選已讀之後，按鈕要新增disabled
                    if(result.trim() == 'success'){
                        $(el).prop("disabled", true);
                        if(id == ''){
                            $("input[name='read']").prop("disabled", true);
                        }
                    }
				},
				error:function(xhr, status, error){
					alert(xhr.statusText);
				}
			});
		}

        function deleteArticles(el, id){
			$.ajax({
				url: "{{ route('deleteArticles') }}", 
				type: 'POST',
				data:{
					"id" : id,
                    "isEven" : id % 2 == 0 ? true : false,
					'_token':'{{csrf_token()}}'
				},
				success: function(result){
                    //點選已讀之後，按鈕要新增disabled
                    if(result.trim() == 'success'){
                        $(el).parent().parent().remove();
                    }
				},
				error:function(xhr, status, error){
					alert(xhr.statusText);
				}
			});
		}

        function getDateDiff(sDate) {
            let now = new Date();
            let days = now.getTime() - sDate.getTime();
            let day = parseInt(days / parseInt(1000 * 60 * 60 * 24));

            if(day == 0){
                day = parseInt(days / parseInt(1000 * 60 * 60));
                if(day == 0){
                    day = parseInt(days / parseInt(1000 * 60));
                    if(day == 0){
                        return parseInt(days / 1000) + ' 秒前'
                    }
                    return day + ' 分前'
                }
                return day + ' 時前'
            }else if(day < 7){
                day = day + ' 天前'
            }else{
                day = sDate.getFullYear() + '-' + sDate.getMonth() + '-' + sDate.getDate();
            }

            return day;
        }

	</script>
</body>

</html>