<footer class="footer">
    <p>Copyright &copy; <?=date('Y')?> | TimeTracker.com</p>
</footer>

<script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="//momentjs.com/downloads/moment.js"></script>

<script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="//www.gstatic.com/charts/loader.js"></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="<?= base_url(); ?>assets/user/javascript/bootstrap-datetimepicker.min.js?v=<?= VERSION ?>"></script>
<script src="<?= base_url(); ?>assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js?v=<?= VERSION ?>"></script>
<script src="<?= base_url(); ?>assets/javascript/addProject.js?v=<?= VERSION ?>"></script>
<script src="<?= base_url(); ?>assets/javascript/Chart.min.js?v=<?= VERSION ?>"></script>
<script src="<?= base_url(); ?>assets/user/javascript/utils.js?v=<?= VERSION ?>"></script>
<script src="<?= base_url(); ?>assets/javascript/addUser.js?v=<?= VERSION ?>"></script>
<script src="<?= base_url(); ?>assets/javascript/addTask.js?v=<?= VERSION ?>"></script>
<script src="<?= base_url(); ?>assets/javascript/admin.js?v=<?= VERSION ?>"></script>
<script src="<?= base_url(); ?>assets/javascript/script.js?v=<?= VERSION ?>"></script>
<script src="<?= base_url(); ?>assets/javascript/project_snapshot.js?v=<?= VERSION ?>"></script>
<script src="<?= base_url(); ?>assets/javascript/task_snapshot.js?v=<?= VERSION ?>"></script>
<script src="<?= base_url(); ?>assets/javascript/edit_project.js?v=<?= VERSION ?>"></script>
<script src="<?= base_url(); ?>assets/javascript/user_snapshot.js?v=<?= VERSION ?>"></script>
<script src="<?= base_url(); ?>assets/javascript/chart.js?v=<?= VERSION ?>"></script>
<script src="<?= base_url(); ?>assets/javascript/user_detail.js?v=<?= VERSION ?>"></script>
<script src="<?= base_url(); ?>assets/javascript/project_details.js?v=<?= VERSION ?>"></script>
<script type="text/javascript">
    $(function() {
        $('.edit-date').datepicker({
            useCurrent: false,
            format: 'yyyy-mm-dd',
        });
    });
</script>
</body>

</html>