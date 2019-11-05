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
                <!-- <p class="text-right"><i class="fas fa-list-ul"></i></p> -->

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
function getDateOfWeek(weekNumber, year) {   /* to convert weeknumber to day format*/
    var input_date;
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
    input_date = input_date+'/'+new_date.slice(8,10)+'/'+new_date.slice(11,15);
    return input_date;

}
var d = "<?php echo $value; ?>" ;
var myDate = getDateOfWeek(d.slice(4,6), d.slice(0,4));
document.getElementById('daily-chart').value=myDate;
<?php } ?>
</script>

<?php include("footer.php"); ?>