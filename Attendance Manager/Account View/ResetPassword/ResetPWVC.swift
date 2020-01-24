/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : ResetPWVC.swift
 //
 //    File Created      : 23:Dec:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : To reset password view controller.
 //
 //////////////////////////////////////////////////////////////////////////// */

import UIKit

class ResetPWVC: UIViewController {

    @IBOutlet weak var txFCurrentPW: UITextField!
    @IBOutlet weak var txFNewPW: UITextField!
    @IBOutlet weak var txFReNewPW: UITextField!
    @IBOutlet weak var btnChangePW: UIButton!
    @IBOutlet weak var btnBack: UIButton!
    @IBOutlet weak var viewMain: UIView!
    @IBOutlet weak var lblError: UILabel!
    @IBOutlet weak var imgPWMatcher: UIImageView!
    @IBOutlet weak var actIndicator: UIActivityIndicatorView!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        view.addGradient(cgPStart: CGPoint(x: 0, y: 0), cgPEnd: CGPoint(x: 1, y: 0.3))
        // Setting navigation swipe recognizer.
        self.navigationController?.interactivePopGestureRecognizer?.delegate = nil
        self.navigationController?.interactivePopGestureRecognizer?.isEnabled = true
        
        let cgPStart = CGPoint(x: 0, y: 0.25)
        let cgPEnd = CGPoint(x: 1, y: 0.75)
        btnChangePW.addGradient(cgPStart: cgPStart, cgPEnd: cgPEnd, cgFRadius: 15)
        btnChangePW.setTitleColor(.white, for: .normal)
        txFCurrentPW.useUnderline(uiColor: UIColor.lightGray.withAlphaComponent(0.3))
        txFNewPW.useUnderline(uiColor: UIColor.lightGray.withAlphaComponent(0.3))
        txFReNewPW.useUnderline(uiColor: UIColor.lightGray.withAlphaComponent(0.3))
        
        viewMain.layer.masksToBounds = true
        viewMain.layer.cornerRadius = 35
        viewMain.backgroundColor = g_colorMode.defaultColor()
        viewMain.layer.borderColor = g_colorMode.lineColor().cgColor
        viewMain.layer.borderWidth = 0.3
    }
    
    @IBAction func btnChangePWPressed(_ sender: Any) {
        self.view.endEditing(true)
        guard txFNewPW.text != "" && txFReNewPW.text != "" && txFCurrentPW.text != "" else {
            lblError.text = "Enter all the fields..!"
            lblError.isHidden = false
            return
        }
        lblError.isHidden = true
        if validatePassword()! {
            actIndicator.startAnimating()
            let oldPw = txFCurrentPW.text!
            let newPW = txFReNewPW.text!
            APIResponseHandler.resetPassword(oldPW: oldPw, newPW: newPW, completion: {
                status, msg in
                if status {
                    let viewNotif = InAppNotificationView()
                    viewNotif.sendNotification(msg: "Password changed successfully",
                                               autoDismiss: true)
                    viewNotif.addGradient()
                    self.view.addSubview(viewNotif)
                    self.txFCurrentPW.text = ""
                    self.txFNewPW.text = ""
                    self.txFReNewPW.text = ""
                }
                else {
                    self.lblError.text = msg
                    self.lblError.isHidden = false
                    self.txFCurrentPW.text = ""
                }
                self.imgPWMatcher.image = nil
                self.actIndicator.stopAnimating()
            })
        }
        else {
            lblError.text = "Passwords doesn't match..!"
            lblError.isHidden = false
            self.txFNewPW.text = ""
            self.txFReNewPW.text = ""
            self.imgPWMatcher.image = nil
        }
    }
    
    @IBAction func btnBackPressed(_ sender: Any) {
        self.navigationController?.popToRootViewController(animated: true)
    }
    
    func validatePassword() -> Bool? {
        guard txFNewPW.text != "" && txFReNewPW.text!.count >= 6 else {
            imgPWMatcher.image = nil
            return false
        }
        // Set password match
        if txFNewPW.text == txFReNewPW.text {
            imgPWMatcher.image = #imageLiteral(resourceName: "successIcon")
            return true
        }
        else {
            imgPWMatcher.image = nil
            return false
        }
    }
    
    @IBAction func txtFEnterPWEditingChanged(_ sender: Any) {
        _ = validatePassword()
    }
    
    @IBAction func txtFReEnterPWEditingChanged(_ sender: Any) {
        _ = validatePassword()
    }
    
    @IBAction func txtOldPWPrimaryAction(_ sender: Any) {
        txFNewPW.becomeFirstResponder()
    }
    
    @IBAction func txtNewPWPrimaryAction(_ sender: Any) {
        txFReNewPW.becomeFirstResponder()
    }
    
    @IBAction func txtReNewPWPrmaryAction(_ sender: Any) {
        self.view.endEditing(true)
    }
    
    @IBAction func viewMainPressed(_ sender: Any) {
        self.view.endEditing(true)
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
            view.layer.sublayers?.removeFirst()
            btnChangePW.layer.sublayers?.removeFirst()
            
            setColorMode()
            updateGradient()
        }
    }
    
    func updateGradient() {
        if let tabController = self.navigationController?.viewControllers[0] as? TabBarController {
            tabController.updateAllViewCtrlrs()
        }
        
        let cgPStart = CGPoint(x: 0, y: 0.25)
        let cgPEnd = CGPoint(x: 1, y: 0.75)
        view.addGradient(cgPStart: CGPoint(x: 0, y: 0), cgPEnd: CGPoint(x: 1, y: 0.3))
        btnChangePW.addGradient(cgPStart: cgPStart, cgPEnd: cgPEnd, cgFRadius: 15)
        view.layer.needsLayout()
    }
}
