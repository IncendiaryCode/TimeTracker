//
//  FPasswordController.swift
//  Attendance Manager
//
//  Created by Sachin on 8/30/19.
//  Copyright © 2019 Sachin. All rights reserved.
//

import UIKit

class FPEmailOtpController: UIViewController {
    
    @IBOutlet weak var txtEmail: UITextField!
    @IBOutlet weak var lblEmailError: UILabel!
    @IBOutlet weak var txtOTP: UITextField!
    @IBOutlet weak var btnOtpSubmit: UIButton!
    @IBOutlet weak var actLoading: UIActivityIndicatorView!
    @IBOutlet weak var btnResendOTP: UIButton!
    @IBOutlet weak var btnEmailSubmit: UIButton!
    @IBOutlet weak var viewResetPswd: UIView!
    @IBOutlet weak var btnCancel: UIButton!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        actLoading.isHidden = true
        txtEmail.useUnderline()
        txtOTP.useUnderline()
        lblEmailError.isHidden = true
        txtOTP.alpha = 0.5
        txtOTP.isEnabled = false
        btnOtpSubmit.alpha = 0.5
        btnOtpSubmit.isEnabled = false
        btnResendOTP.alpha = 0.5
        btnResendOTP.isEnabled = false
        let cgSize = CGSize(width: UIScreen.main.bounds.width, height: UIScreen.main.bounds.height)
        self.view.frame.size = cgSize
        print(self.view.frame)
        view.addGradient(startColor: cgCForGradientStart, endColor: cgCForGradientEnd)
                self.viewResetPswd.addGradient(startColor: cgCForGradientStart,
                                               endColor: cgCForGradientEnd, cgFRadius: 50)
        self.btnEmailSubmit.addGradient(startColor: cgCForGradientStart,
                                        endColor: cgCForGradientEnd, cgFRadius: 15)
        self.btnOtpSubmit.addGradient(startColor: cgCForGradientStart,
                                      endColor: cgCForGradientEnd, cgFRadius: 15)
        self.btnCancel.addGradient(startColor: cgCForGradientStart,
                                   endColor: cgCForGradientEnd, cgFRadius: 15)
        // Do any additional setup after loading the view.
    }
    
    @IBAction func btnEmailSearchClicked(_ sender: Any) {
        //Checks email validation.
        if !checkEmailValidation(strEmail: txtEmail.text!) {
            btnEmailSubmit.isEnabled = false
            lblEmailError.isHidden = true
            actLoading.isHidden = false
            txtEmail.text = ""
            actLoading.startAnimating()
            self.view.endEditing(true)
            DispatchQueue.global(qos: .background).async {
                sleep(1)
                DispatchQueue.main.async {
                    self.txtOTP.alpha = 1.0
                    self.txtOTP.isEnabled = true
                    self.btnOtpSubmit.alpha = 1.0
                    self.btnOtpSubmit.isEnabled = true
                    self.actLoading.stopAnimating()
                    self.actLoading.isHidden = true
                    self.btnResendOTP.isEnabled = true
                    self.btnResendOTP.alpha = 1.0
                }
            }
        }
        else {
            lblEmailError.isHidden = false
        }
    }
    
    @IBAction func btnOtpPressed(_ sender: Any) {
        //Submits OTP to the server.
        self.view.endEditing(true)
        performSegue(withIdentifier: "ResetPassword", sender: nil)
        lblEmailError.isHidden = true
        txtOTP.alpha = 0.5
        txtOTP.isEnabled = false
        btnOtpSubmit.alpha = 0.5
        btnOtpSubmit.isEnabled = false
        btnResendOTP.alpha = 0.5
        btnResendOTP.isEnabled = false
        txtOTP.text = ""
        txtEmail.text = ""
    }
    
    @IBAction func viewClicked(_ sender: Any) {
        self.view.endEditing(true)
    }
    
    @IBAction func btnCancelToLoginPagePressed(_ sender: Any) {
        //performSegue(withIdentifier: "LoginView", sender: nil)
        self.dismiss(animated: true, completion: nil)
    }
    
    @IBAction func btnResendOtpPressed(_ sender: Any) {
        
    }
}

