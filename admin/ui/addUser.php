<?php include("header.php"); ?>
<body>
    <main class="container-fluid container-fluid-main">
        <div class="container main-container">
            <div class="main-container-inner">
                <h1 class="text-center m-4">Add User</h1>
                <div class="container pt-5">
                        <form action="../php/add_user.php" id="addUser" method="post">
                            <div class="form-group mt-3 row">
                                <label for="task-name" class="col-md-3 text-left">Enter first name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control col-md-9 text-left has-empty-validation" name="first_name" id="firstName">
                            </div>
                            <div class="form-group row mt-5">
                                <label for="task-name " class="col-md-3 text-left">Enter last name</label>
                                <input type="text" class="form-control col-md-9 text-left" name="last_name" id="lastName">
                            </div>
                            <div class="form-group row mt-5">
                                <label for="task-name" class="col-md-3 text-left">Enter email<span class="text-danger">*</span></label>
                                <input type="email" class="form-control col-md-9 text-left has-empty-validation has-email-validation " name="user_email" id="user_email" novalidate>
                            </div>
                            <div class="form-group row mt-5">
                                <label for="task-name" class="col-md-3 text-left">Enter the contact number</label>
                                <input type="tel" minlength="10" maxlength="10" class="form-control col-md-9 text-left" name="contact" id="contact">
                            </div>
                            <div class="form-group row mt-5">
                                <label for="task-name" class="col-md-3 text-left">Enter password<span class="text-danger">*</span></label>
                                <input type="password" class="form-control col-md-9 text-left has-empty-validation" name="user_password">
                            </div>
                            <div class="text-right">
                            <p id="error" class="text-danger"></p>
                                <button type="submit" class="btn btn-primary save-task">Add user</button>
                            </div>
                        </form>
                </div>
            </div>
        </div>
    </main>
    <hr>
    <footer>
        <p class="text-center p-3 ">Copyright © 2019 Printgreener.com</ p>
    </footer>
<?php include("footer.php"); ?>