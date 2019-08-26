//
//  FPResetPController.swift
//  Attendance Manager
//
//  Created by Sachin on 9/3/19.
//  Copyright © 2019 Sachin. All rights reserved.
//

import UIKit

class FPResetPController: UIViewController {

    @IBOutlet weak var txtResetPswd: UITextField!
    @IBOutlet weak var txtResetRePswd: UITextField!
    @IBOutlet weak var lblErrPswd: UILabel!
    @IBOutlet weak var actResetPassword: UIActivityIndicatorView!
    @IBOutlet weak var viewReset: UIView!
    @IBOutlet weak var btnCancel: UIButton!
    @IBOutlet weak var btnReset: UIButton!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.view.addGradient(startColor: cgCForGradientStart, endColor: cgCForGradientEnd)
        self.viewReset.addGradient(startColor: cgCForGradientStart,
                                   endColor: cgCForGradientEnd, cgFRadius: 50)
        self.btnCancel.addGradient(startColor: cgCForGradientStart,
                                   endColor: cgCForGradientEnd, cgFRadius: 15)
        self.btnReset.addGradient(startColor: cgCForGradientStart,
                                  endColor: cgCForGradientEnd, cgFRadius: 15)
        txtResetPswd.useUnderline()
        txtResetRePswd.useUnderline()
        actResetPassword.isHidden = true
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
    
    @IBAction func btnResetPswdPressed(_ sender: Any) {
        //Send new password to the server.
        if txtResetPswd.text != txtResetRePswd.text {
            lblErrPswd.isHidden = false
        }
        else {
            lblErrPswd.isHidden = true
            actResetPassword.isHidden = false
            view.alpha = 0.8
            actResetPassword.alpha = 1.0
            actResetPassword.startAnimating()
            DispatchQueue.global(qos: .background).async {
                sleep(1)
                DispatchQueue.main.async {
                    self.actResetPassword.stopAnimating()
                    self.performSegue(withIdentifier: "SuccessPage", sender: nil)
                }
            }
        }
    }
    
    @IBAction func btnCancelPressed(_ sender: Any) {
        self.view.window!.rootViewController?.dismiss(animated: false, completion: nil)
    }
}
