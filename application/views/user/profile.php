<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
$GLOBALS['page_title'] = 'My profile';
$this->load->helper('url_helper');
//$this->load->library('session');
//$profile = $this->session->userdata('user_profile');



$picture = substr($res['profile'],30);
?>
<main class="container-fluid container-fluid-main">
    <div class="main-container container">
        <div class="main-container-inner">
            <div class="row mt-5">
                <div class="col-6 offset-3">
                    <div class="text-center mt-4">
                        <img src="<?=base_url().$picture?>" width="30%;" class="rounded-circle figure mt-4 text-center">
                        <h4 class="text-center employee-name mt-3">
                            <?php echo $res['name'];?>
                        </h4>
                    </div>
                </div>
                <div class="col-12">
                    <div class="dropdown text-right" id="dropdown-recent-acts">
                        <i class="fas fa-sliders-h" id="dropdown-recent-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-recent-btn">
                            <!-- sorting options -->
                            <div class="dropdown-item checkbox"><input type="checkbox"  id="dark-mode" name="dark-mode"> Dark mode</div>
                            <div class="dropdown-item checkbox"><input type="checkbox"  id="normal-mode" name="normal-mode"> Normal mode</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="offset-4 col-4 pl-5">
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
        <hr>
        <footer  class="profile-footer">
            <p class="text-center pt-2 ">Copyright © 2019 Printgreener.com</p>
        </footer>
    </div>
</main>