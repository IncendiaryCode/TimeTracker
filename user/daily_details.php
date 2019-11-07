<?php
$GLOBALS['page_title'] = 'Daily activities';
include("header.php");
?>
<main class="container-fluid container-fluid-main">
    <div class="main-container container">
        <div class="main-container-inner">
            <div class="daily-chart">
                <div class="form-group">
                    <label for="end-date">Select date</label>
                    <div class="input-group">

                        <!-- chart that shows daily activities -->
                        <input type="date" class="form-control" id="daily-chart">
                        <button class="btn btn-primary" onclick="dailyChart()">view chart</button>
                    </div>
                </div>

                <div class="dropdown text-right">
                  <button class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                   <i class="fas fa-list-ul"></i>
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="../user/weekly_details.php">Weekly chart</a>
                    <a class="dropdown-item" href="../user/monthly_details.php">Monthly chart</a>
                  </div>
                </div>
                <canvas id="daily"></canvas>
            </div>
        </div>
        <hr class="mt-5">
        <footer>
            <p class="text-center pt-2 ">Copyright © 2019 Printgreener.com</p>
        </footer>
    </div>
</main>
<?php 
if(isset($_GET['value']))
{
$value = $_GET['value'];
?>
<script type="text/javascript">
function getDateOfWeek(weekNumber, year, day) {   /* to convert weeknumber to date format*/
    var input_date;
    var input_day;
    var new_date = new Date(year, 0, 1 + ((weekNumber - 1) * 7)).toString();
    switch(new_date.slice(4,7))
    {
        /*assign month number*/
        case "Jan": input_date='1'; break;
        case "Feb": input_date='2'; break;
        case "Mar": input_date='3'; break;
        case "Apr": input_date='4'; break;
        case "May": input_date='5'; break;
        case "Jun": input_date='6'; break; 
        case "Jul": input_date='7'; break;
        case "Aug": input_date='8'; break;
        case "Sep": input_date='9'; break;
        case "Oct": input_date='10'; break;
        case "Nov": input_date='11'; break;
        case "Dec": input_date='12'; break;
    }
    switch(day)
    {
        /*assign day number*/
        case "Sun": input_day='0'; break;
        case "Mon": input_day='1'; break;
        case "Tue": input_day='2'; break;
        case "Wed": input_day='3'; break;
        case "Thu": input_day='4'; break;
        case "Fri": input_day='5'; break;
        case "Sat": input_day='6'; break;
    }
    var int_day = parseInt(new_date.slice(8,10));
    input_day = (((parseInt(input_day)-1)+parseInt(int_day)-1)%31);
    input_date = input_date+'/'+input_day+'/'+new_date.slice(11,15);
    return input_date;
}
var d = "<?php echo $value; ?>" ;

var myDate = getDateOfWeek((parseInt(d.slice(4,6))), (parseInt(d.slice(0,4))), (d.slice(6,9)));
console.log(myDate);
document.getElementById('daily-chart').value = myDate;
<?php } ?>
</script>

<?php include("footer.php"); ?>