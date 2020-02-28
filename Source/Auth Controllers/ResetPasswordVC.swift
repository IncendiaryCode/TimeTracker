/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : ResetPasswordVC.swift
 //
 //    File Created      : 03:Sept:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : Reset password view controller.
 //
 //////////////////////////////////////////////////////////////////////////// */

import UIKit

class ResetPasswordVC: UIViewController {

    @IBOutlet weak var txtResetPswd: UITextField!
    @IBOutlet weak var txtResetRePswd: UITextField!
    @IBOutlet weak var lblErrPswd: UILabel!
    @IBOutlet weak var actResetPassword: UIActivityIndicatorView!
    @IBOutlet weak var viewReset: UIView!
    @IBOutlet weak var btnCancel: UIButton!
    @IBOutlet weak var btnReset: UIButton!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        self.view.addGradient()
        
        // Tap getsure to view
        let tapView = UITapGestureRecognizer(target: self, action: #selector(viewClickToDismiss(_:)))
        view.addGestureRecognizer(tapView)
        
        let cgPStart = CGPoint(x: 0, y: 0.25)
        let cgPEnd = CGPoint(x: 1, y: 0.75)
        self.btnCancel.addGradient(cgPStart: cgPStart, cgPEnd: cgPEnd, cgFRadius: 15)
        self.btnReset.addGradient(cgPStart: cgPStart, cgPEnd: cgPEnd, cgFRadius: 15)
        
        txtResetPswd.useUnderline()
        txtResetRePswd.useUnderline()
        actResetPassword.hidesWhenStopped = true
    }
    
    @IBAction func viewPressed(_ sender: Any) {
        self.view.endEditing(true)
    }
    
    @IBAction func btnEndPswdEditing(_ sender: Any) {
        if txtResetPswd.text != txtResetRePswd.text {
            lblErrPswd.isHidden = false
        }
        else {
            lblErrPswd.isHidden = true
        }
    }
    
    /// Send new password to the server.
    @IBAction func btnResetPswdPressed(_ sender: Any) {
        if txtResetPswd.text!.count < 6  && txtResetPswd.text!.count < 6 {
            lblErrPswd.text = "Password should contain atleast 6 character"
            lblErrPswd.isHidden = false
            self.txtResetPswd.shakeTextField()
            self.txtResetRePswd.shakeTextField()
        }
        else if txtResetPswd.text != txtResetRePswd.text {
            lblErrPswd.text = "Passwords should match"
            lblErrPswd.isHidden = false
            self.txtResetPswd.shakeTextField()
            self.txtResetRePswd.shakeTextField()
        }
        else {
            lblErrPswd.isHidden = true
            view.alpha = 0.8
            actResetPassword.alpha = 1.0
            actResetPassword.startAnimating()
            
            let password = txtResetRePswd.text!
            APIResponseHandler.resetOtpPassword(newPW: password, completion: {
                status, msg in
                if status {
                    print("Reset password success")
                    let vc = self.navigationController?.viewControllers.first
                        as! LoginViewController
                    self.navigationController?.popToRootViewController(animated: true)
                    let viewNotif = InAppNotificationView()
                    vc.view.addSubview(viewNotif)
                    viewNotif.sendNotification(msg: "Reset password success, Please login now")
                    viewNotif.addGradient()
                }
                else {
                    self.lblErrPswd.text = msg
                    self.lblErrPswd.isHidden = true
                    self.txtResetPswd.shakeTextField()
                    self.txtResetRePswd.shakeTextField()
                }
                self.actResetPassword.stopAnimating()
            })
            
        }
    }
    
    @IBAction func txtPWPrimaryAction(_ sender: Any) {
        txtResetRePswd.becomeFirstResponder()
    }
    
    @IBAction func txtRePWPrimaryAction(_ sender: Any) {
        txtResetRePswd.endEditing(true)
    }
    
    /// Method will called when user clicks on this view anywhere.
    @objc func viewClickToDismiss(_ sender: Any) {
        self.view.endEditing(true)
    }
    
    @IBAction func btnCancelPressed(_ sender: Any) {
        self.navigationController?.popToRootViewController(animated: true)
    }
    
    override func traitCollectionDidChange(_ previousTraitCollection: UITraitCollection?) {
        super.traitCollectionDidChange(previousTraitCollection)
        
        guard UIApplication.shared.applicationState == .inactive else {
            return
        }
        
        if #available(iOS 12.0, *) {
            if self.traitCollection.userInterfaceStyle == .light {
                UserDefaults.standard.setValue(1, forKey: "colorMode")
            }
            else {
                UserDefaults.standard.setValue(2, forKey: "colorMode")
            }
            // Remove old gradients.
            self.view.layer.sublayers?.removeFirst()
            self.btnCancel.layer.sublayers?.removeFirst()
            self.btnReset.layer.sublayers?.removeFirst()
            
            setColorMode()
            updateGradient()
        }
    }
    
    func updateGradient() {
        self.view.addGradient()
        let cgPStart = CGPoint(x: 0, y: 0.25)
        let cgPEnd = CGPoint(x: 1, y: 0.75)
        
        self.btnCancel.addGradient(cgPStart: cgPStart, cgPEnd: cgPEnd, cgFRadius: 15)
        self.btnReset.addGradient(cgPStart: cgPStart, cgPEnd: cgPEnd, cgFRadius: 15)
        view.layer.needsLayout()
    }
}
