//
//  ViewController.swift
//  Attendance Manager
//
//  Created by Sachin on 8/29/19.
//  Copyright © 2019 Sachin. All rights reserved.
//

import UIKit
import SystemConfiguration.CaptiveNetwork

class LoginController: UIViewController {

    @IBOutlet weak var lblErrorValidator: UILabel!
    @IBOutlet weak var txtFEmail: UITextField!
    @IBOutlet weak var txtFPassword: UITextField!
    @IBOutlet weak var btnLogin: UIButton!
    @IBOutlet weak var viewLogin: UIView!
    @IBOutlet weak var imgVLogo: UIImageView!
    @IBOutlet weak var btnForgetPswd: UIButton!
    @IBOutlet weak var imgVSplashLogo: RadioWaveAnimationView!
    @IBOutlet weak var actView: UIActivityIndicatorView!
    var nSleepTime: UInt32!
    var requestController: RequestController!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.view.addGradient(startColor: cgCForGradientStart, endColor: cgCForGradientEnd)
        txtFEmail.isHidden = true
        txtFPassword.isHidden = true
        btnForgetPswd.isHidden = true
        btnLogin.isHidden = true
        imgVLogo.isHidden = true
        nSleepTime = 0
    }
    
    override func viewDidAppear(_ animated: Bool) {
        super.viewDidAppear(true)
        sleep(nSleepTime)
        let status = UserDefaults.standard.bool(forKey: "userLogin")
        if status == true {
            performSegue(withIdentifier: "LoginSuccess", sender: nil)
        }
        else {
            txtFEmail.isHidden = false
            txtFPassword.isHidden = false
            btnForgetPswd.isHidden = false
            btnLogin.isHidden = false
            imgVLogo.isHidden = false
            //imgVSplashLogo.isHidden = true
            self.txtFEmail.useUnderline()
            self.txtFPassword.useUnderline()
            self.viewLogin.addGradient(startColor: cgCForGradientStart, endColor: cgCForGradientEnd,
                                       cgFRadius: 50)
            self.btnLogin.addGradient(startColor: cgCForGradientStart, endColor: cgCForGradientEnd,
                                      cgFRadius: 15)
            if let email = UserDefaults.standard.value(forKey: "Email") {
                txtFEmail.text = (email as! String)
            }
        }
        imgVSplashLogo.isHidden = true
    }
    
    @IBAction func txtFMoveToNextField(_ sender: Any) {
        //Calls when user clicks next key in keyboard from Email textview.
        txtFPassword.becomeFirstResponder()
    }
    
    @IBAction func txtFEndPasswordEditing(_ sender: Any) {
        //Calls when user finish typing password.
        txtFPassword.endEditing(true)
    }
    
    @IBAction func viewPressed(_ sender: Any) {
        self.view.endEditing(true)
    }
    
    @IBAction func viewClickToDismiss(_ sender: Any) {
        //Method will called when user clicks on this view anywhere.
        self.view.endEditing(true)
    }
    deinit {
        print("Called Deeinit")
    }
    
    @IBAction func btnLoginPressed(_ sender: Any) {
        //Email and Password Validation.
        if txtFEmail.text == "" && txtFPassword.text == "" {
            lblErrorValidator.text = "Fill all the Fields..!"
            lblErrorValidator.isHidden = false
            shakeTextField(textField: txtFEmail)
            shakeTextField(textField: txtFPassword)
            getWIFIInformation()
            getWeekNumber(strDate: "12/09/2019")
//            performSegue(withIdentifier: "LoginSuccess", sender: nil)
        }
//        else if !checkEmailValidation(strEmail: txtFEmail.text!) && txtFPassword.text != "" {
//            shakeTextField(textField: txtFEmail)
//            lblErrorValidator.text = "Email or Password Incorrect..!"
//            lblErrorValidator.isHidden = false
//        }
//        else if checkEmailValidation(strEmail: txtFEmail.text!) && txtFEmail.text == "" {
//            shakeTextField(textField: txtFPassword)
//        }
        else {
            //API validation.
            let strUserName = txtFEmail.text
            let strPswd = txtFPassword.text
            requestController = RequestController()
            if !requestController.isConnectedToNetwork() {
                let alert = UIAlertController(title: "Alert", message:
                    "Your not connected to the internet.",
                                              preferredStyle: UIAlertController.Style.alert)
                alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default,
                                              handler: nil))
                self.txtFPassword.text = ""
                self.present(alert, animated: true, completion: nil)
            }
            else {
                let params = ["email": strUserName, "password": strPswd] as! Dictionary<String, String>
                let strUrl = "https://laatli.com/api/web/v1/users/login_validate?formate_resp=10"
                self.view.endEditing(true)
                self.actView.startAnimating()
                self.btnLogin.alpha = 0.2
                requestController.requestLogin(params: params, url: strUrl) { [weak self] dictResult in
                    DispatchQueue.main.async {
                        self!.actView.stopAnimating()
                        self!.btnLogin.alpha = 1
                        print(dictResult)
                        let strMAC = self?.getWIFIInformation()
                        let arrMAC = self?.requestController.requestMACAddressList()
                        if
    //                        (arrMAC?.contains(strMAC!))! &&
                                dictResult["success"] as! Int == 1{
                            print("Login Success")
                            let dictResName = dictResult["data"] as! Dictionary<String, Any>
                            var strUserName = ""
                            if let strName = dictResName["name"] {
                                strUserName = strName as! String
                            }
                            UserDefaults.standard.set(self!.txtFEmail.text, forKey: "Email")
                            UserDefaults.standard.set(strUserName, forKey: "Username")
                            self!.lblErrorValidator.isHidden = true
                            self!.txtFEmail.text = ""
                            self!.txtFPassword.text = ""
                            
                            self!.performSegue(withIdentifier: "LoginSuccess", sender: nil)
                        }
                        else if dictResult["success"] as! Int == 1 {
                            let alert = UIAlertController(title: "Alert", message: "Your not connected to the company wifi.", preferredStyle: UIAlertController.Style.alert)
                            alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default, handler: nil))
                            self!.txtFPassword.text = ""
                            self!.present(alert, animated: true, completion: nil)
                        }
                        else {
                            print("Login Failed")
                            self!.lblErrorValidator.isHidden = false
                            self!.txtFPassword.text = ""
                            self!.lblErrorValidator.text = "Incorrect username or password"
                        }
                    }
                    
                    // This will get called once the login request has completed. The login might have succeeded of failed, but here you can make the decision to show the user some indication of that
                }
            }
        }
    }
    
    @IBAction func btnFPasswordPressed(_ sender: Any) {
//        getWIFIInformation()
        performSegue(withIdentifier: "FindAcWithEmail", sender: nil)
    }
    
    func getWIFIInformation() -> String{
        let informationArray:NSArray? = CNCopySupportedInterfaces()
        var strMAC = ""
        if let information = informationArray {
            print(information.count)
            let dict:NSDictionary? = CNCopyCurrentNetworkInfo(information[0] as! CFString)
            if let temp = dict {
//                informationDictionary["SSID"] = String(temp["SSID"]!)
//                informationDictionary["BSSID"] = String(temp["BSSID"]!)
                strMAC = temp["BSSID"] as! String
                print(temp["BSSID"] as! String)
            }
        }
        return strMAC
    }
    
    func shakeTextField(textField: UITextField)
    {
        let animation = CABasicAnimation(keyPath: "position")
        animation.duration = 0.07
        animation.repeatCount = 3
        animation.autoreverses = true
        animation.fromValue = NSValue(cgPoint: CGPoint(x: textField.center.x - 10, y:
            textField.center.y))
        animation.toValue = NSValue(cgPoint: CGPoint(x: textField.center.x + 10, y:
            textField.center.y))
        textField.layer.add(animation, forKey: "position")
//        textField.attributedPlaceholder = NSAttributedString(string: textField.placeholder ?? "",
//            attributes: [NSAttributedString.Key.foregroundColor: UIColor.red])
        
    }
    
}
extension UITextField {
    func useUnderline() {
        //To provide underline for text field.
        let border = CALayer()
        let borderWidth = CGFloat(1.0)
        border.borderColor = UIColor.white.cgColor
        border.frame = CGRect(origin: CGPoint(x: 0,y :self.frame.size.height - borderWidth),
                    size: CGSize(width: self.frame.size.width, height: self.frame.size.height))
        border.borderWidth = borderWidth
        self.layer.addSublayer(border)
        self.layer.masksToBounds = true
    }
}

func checkEmailValidation(strEmail: String) -> Bool {
    //Email validator.
    let strRegEx = "[A-Z0-9a-z._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,64}"
    let nsPredEmail = NSPredicate(format: "SELF MATCHES %@", strRegEx)
    let bIsValidEmail = nsPredEmail.evaluate(with: strEmail)
    return bIsValidEmail
}
