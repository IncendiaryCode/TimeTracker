var id = localStorage.getItem('count');
console.log('id',id);
for (var i = id; i > 0; i--) {
    variable = localStorage.getItem('entry' + parseInt(i));
    var data = JSON.parse(variable);
    var loginTime = data.date;
    var started = data.started;
    var ended = data.ended;
    var timeUsed = data.timeUsed;
    console.log(data)
    
    $(".au-task__item-inner").append("<h5 class='task'><p>"+loginTime+"</p></h5><h6><span class='time'>"+started+"</span><span class='time ml-5'>"+ended+"</span></h6><span>"+timeUsed+"</span><hr>");
}
