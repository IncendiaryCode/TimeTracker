<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
$GLOBALS['page_title'] = 'My profile';
$this->load->helper('url_helper');
//$this->load->library('session');
//$profile = $this->session->userdata('user_profile');



$picture = substr($res['profile'],30);
?>
<main class="container-fluid-main">
    <div class="main-container container">
        <div class="main-container-inner">
            <div class="row mt-5">
                <div class="col-md-6 offset-md-3">
                    <div class="text-center">
                        <img src="<?=base_url().$picture?>" width="30%;" class="rounded-circle figure mt-4 text-center">
                        <h4 class="text-center employee-name mt-3">
                            <?php echo $res['name'];?>
                        </h4>
                    </div>
                </div>
                <div class="col-12">
                    <form method="post" action="<?=base_url();?>index.php/user/dark" id="dark-mode">
                        <div class="dropdown text-right" id="dropdown-recent-acts">
                            <i class="fas fa-sliders-h" id="dropdown-recent-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-recent-btn">

                                <div class="dropdown-item checkbox"><input type="hidden" name="status" id="hidden-status"><input type="checkbox"  id="dark-mode-checkbox" name="dark-mode"> Dark mode</div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="offset-md-4 col-md-4">
                    <table class="table mt-5">
                        <tbody id="table-body">
                            <table>
                                <tbody>
                                    <tr>
                                        <th width="50%">Email :</th>
                                        <td width="50%">
                                            <?php echo $res['email'];?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th width="50%">Phone number :</th>
                                        <td width="50%">
                                            <?=$res['phone'];?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Type :</th>
                                        <td>
                                            <?=$res['type'];?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <?php
        if(!empty($GLOBALS['dark_mode'])){
    if ($GLOBALS['dark_mode'] == 1) { ?>
        <script type="text/javascript">        
                $('#dark-mode-checkbox').attr("checked","checked"); 
        </script>
        <?php }  else { ?>
        <script type="text/javascript">
                $('#dark-mode-checkbox').removeAttr("checked");
        </script>
      <?php }} ?> 
        <footer  class="profile-footer">
        <hr>
            <p class="text-center">Copyright © 2019 Printgreener.com</p>
        </footer>
    </div>
</main>