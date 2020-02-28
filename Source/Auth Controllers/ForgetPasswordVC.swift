/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : ForgetPasswordVC.swift
 //
 //    File Created      : 30:Sept:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : Forget password view controller handles OTP validation.
 //
 //////////////////////////////////////////////////////////////////////////// */

import UIKit

class ForgetPasswordVC: UIViewController {
    
    @IBOutlet weak var txtEmail: UITextField!
    @IBOutlet weak var lblEmailError: UILabel!
    @IBOutlet weak var txtOTP: UITextField!
    @IBOutlet weak var btnOtpSubmit: UIButton!
    @IBOutlet weak var actLoading: UIActivityIndicatorView!
    @IBOutlet weak var btnResendOTP: UIButton!
    @IBOutlet weak var btnEmailSubmit: UIButton!
    @IBOutlet weak var viewResetPswd: UIView!
    @IBOutlet weak var btnCancel: UIButton!
    @IBOutlet weak var nsLTxtOtpTop: NSLayoutConstraint!
    @IBOutlet weak var btnCancelFirst: UIButton!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        actLoading.hidesWhenStopped = true
        txtEmail.useUnderline()
        txtOTP.useUnderline()
        initialViewSetup()
        
        // Tap getsure to view
        let tapView = UITapGestureRecognizer(target: self, action: #selector(viewClickToDismiss(_:)))
        view.addGestureRecognizer(tapView)
        
        let cgSize = CGSize(width: UIScreen.main.bounds.width, height: UIScreen.main.bounds.height)
        self.view.frame.size = cgSize
        
        view.addGradient()
        let cgPStart = CGPoint(x: 0, y: 0.25)
        let cgPEnd = CGPoint(x: 1, y: 0.75)
        
        self.btnEmailSubmit.addGradient(cgPStart: cgPStart, cgPEnd: cgPEnd, cgFRadius: 15)
        self.btnOtpSubmit.addGradient(cgPStart: cgPStart, cgPEnd: cgPEnd, cgFRadius: 15)
    }
    
    /// Setup initial view
    func initialViewSetup() {
        // Initially disable OTP fields.
        self.nsLTxtOtpTop.constant = 20
        lblEmailError.isHidden = true
        txtOTP.isHidden = true
        btnOtpSubmit.isHidden = true
        btnEmailSubmit.isEnabled = true
        btnEmailSubmit.setTitle("Submit", for: .normal)
        btnOtpSubmit.setTitle("Submit", for: .normal)
        nsLTxtOtpTop.constant = 0
        btnResendOTP.isHidden = true
        btnCancel.isHidden = true
    }
    
    @IBAction func btnEmailSearchClicked(_ sender: Any) {
        //Checks email validation.
        if isValidEmail(strEmail: txtEmail.text!) {
            btnEmailSubmit.setTitle("", for: .normal)
            btnEmailSubmit.isEnabled = false
            let email = txtEmail.text!
            lblEmailError.isHidden = true
            actLoading.startAnimating()
            self.view.endEditing(true)
            
            APIResponseHandler.sendOTP(email: email, completion: {
                status, msg in
                if status {
                    // Notify by InAppNotication.
                    let viewNotif = InAppNotificationView()
                    self.view.addSubview(viewNotif)
                    viewNotif.sendNotification(msg: "OTP has been sent to your email id.")
                    viewNotif.addGradient()
                    
                    self.txtOTP.alpha = 1.0
                    self.txtOTP.isHidden = false
                    self.btnOtpSubmit.alpha = 1.0
                    self.btnOtpSubmit.isHidden = false
                    self.actLoading.isHidden = true
                    self.btnResendOTP.isHidden = false
                    self.btnResendOTP.alpha = 1.0
                    self.txtEmail.isHidden = true
                    self.btnEmailSubmit.isHidden = true
                    self.btnCancelFirst.isHidden = true
                    self.btnCancel.isHidden = false
                    self.actLoading.center = self.btnOtpSubmit.center
                    
                    self.nsLTxtOtpTop.constant = 20
                    UIView.animate(withDuration: 0.5) {
                        self.view.layoutIfNeeded()
                    }
                }
                else {
                    self.initialViewSetup()
                    self.lblEmailError.isHidden = false
                    self.lblEmailError.text = msg
                    self.txtEmail.shakeTextField()
                }
                self.actLoading.stopAnimating()
            })
        }
        else {
            lblEmailError.isHidden = false
            lblEmailError.text = "Enter valid email"
            txtEmail.shakeTextField()
        }
    }
    
    @IBAction func txtEmailPrimaryAction(_ sender: Any) {
        txtEmail.endEditing(true)
    }
    
    @IBAction func txtOTPPrimaryAction(_ sender: Any) {
        txtOTP.endEditing(true)
    }
    
    /// Reset if invalid otp.
    func reSetupOTPView() {
        btnOtpSubmit.isEnabled = true
        txtOTP.text = ""
        btnOtpSubmit.setTitle("Submit", for: .normal)
    }
    
    /// Submits OTP to the server.
    @IBAction func btnOtpPressed(_ sender: Any) {
        self.view.endEditing(true)
        self.actLoading.center = btnOtpSubmit.center
//        viewResetPswd.bringSubviewToFront(actLoading)
        btnOtpSubmit.setTitle("", for: .normal)
        btnOtpSubmit.isEnabled = false
        lblEmailError.isHidden = true
        guard txtOTP.text!.count == 6 else {
            txtOTP.shakeTextField()
            lblEmailError.isHidden = false
            lblEmailError.text = "Invalid OTP"
            if txtOTP.text!.count == 0 {
                lblEmailError.text = "Enter OTP"
            }
            btnOtpSubmit.isEnabled = true
            txtOTP.text = ""
            btnOtpSubmit.setTitle("Submit", for: .normal)
            return
        }
        
        let email = txtEmail.text!
        let otp = txtOTP.text!
        actLoading.startAnimating()
        APIResponseHandler.validateOTP(email: email, otp: otp, completion: {
            status, msg in
            if status {
                self.lblEmailError.isHidden = true
                UserDefaults.standard.set(self.txtEmail.text, forKey: "userEmail")
                self.performSegue(withIdentifier: "ResetPassword", sender: nil)
            }
            else {
                self.reSetupOTPView()
                self.lblEmailError.text = msg
                self.lblEmailError.isHidden = false
                self.txtOTP.shakeTextField()
            }
            self.actLoading.stopAnimating()
        })
    }
    
    /// Method will called when user clicks on this view anywhere.
    @objc func viewClickToDismiss(_ sender: Any) {
        self.view.endEditing(true)
    }
    
    @IBAction func viewClicked(_ sender: Any) {
        self.view.endEditing(true)
    }
    
    /// performSegue(withIdentifier: "LoginView", sender: nil)
    @IBAction func btnCancelToLoginPagePressed(_ sender: Any) {
//        self.dismiss(animated: true, completion: nil)
        if let viewLogin = navigationController?.viewControllers.first as? LoginViewController {
            viewLogin.nSleepTime = 0
            self.navigationController?.popToRootViewController(animated: true)
        }
    }
    
    @IBAction func btnResendOtpPressed(_ sender: Any) {
        self.lblEmailError.isHidden = true
        self.actLoading.center = btnResendOTP.center
        btnResendOTP.setTitle("", for: .normal)
        actLoading.startAnimating()
        if let email = self.txtEmail.text {
            APIResponseHandler.sendOTP(email: email, completion: {
                status, msg in
                if status {
                    let viewNotif = InAppNotificationView()
                    viewNotif.sendNotification(msg: "OTP re-sent successfully",
                                               autoDismiss: true)
                    viewNotif.addGradient()
                    self.view.addSubview(viewNotif)
                }
                else {
                    self.lblEmailError.isHidden = false
                    self.lblEmailError.text = msg
                }
                self.btnResendOTP.setTitle("Resend", for: .normal)
                self.actLoading.stopAnimating()
            })
        }
    }
    
    override func traitCollectionDidChange(_ previousTraitCollection: UITraitCollection?) {
        super.traitCollectionDidChange(previousTraitCollection)
        
        guard UIApplication.shared.applicationState == .inactive else {
            return
        }
        
        if #available(iOS 12.0, *) {
            // Remove old gradients.
            self.view.layer.sublayers?.removeFirst()
            self.btnEmailSubmit.layer.sublayers?.removeFirst()
            self.btnOtpSubmit.layer.sublayers?.removeFirst()
            
            setColorMode()
            updateGradient()
        }
    }
    
    func updateGradient() {
        self.view.addGradient()
        let cgPStart = CGPoint(x: 0, y: 0.25)
        let cgPEnd = CGPoint(x: 1, y: 0.75)
        
        self.btnEmailSubmit.addGradient(cgPStart: cgPStart, cgPEnd: cgPEnd, cgFRadius: 15)
        self.btnOtpSubmit.addGradient(cgPStart: cgPStart, cgPEnd: cgPEnd, cgFRadius: 15)
        view.layer.needsLayout()
        if let loginVC = self.navigationController?.viewControllers[0] as? LoginViewController {
            loginVC.updateGradient()
        }
    }
}

