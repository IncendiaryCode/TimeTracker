<<<<<<< HEAD
/*var id = localStorage.getItem('count');
console.log('id',id);
=======
var id = localStorage.getItem('count');

>>>>>>> 8f08aeaa5781360dc38d4f4cb6e97c2997478051
for (var i = id; i > 0; i--) {
    variable = localStorage.getItem('entry' + parseInt(i));
    var data = JSON.parse(variable);
    var loginTime = data.date;
    var started = data.started;
    var ended = data.ended;
    var timeUsed = data.timeUsed;
    console.log(data)
    
    $(".au-task__item-inner").append("<h5 class='task'><p>"+loginTime+"</p></h5><h6><span class='time'>"+started+"</span><span class='time ml-5'>"+ended+"</span></h6><span>"+timeUsed+"</span><hr>");
}*/
