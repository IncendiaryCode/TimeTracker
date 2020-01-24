/*//////////////////////////////////////////////////////////////////////////////
//
//    Copyright (c) GreenPrint Technologies LLC. 2019
//
//    File Name         : LoginController.swift
//
//    File Created      : 29:Aug:2019
//
//    Dev Name          : Sachin Kumar K.
//
//    Description       : Login View Controller.
//
//////////////////////////////////////////////////////////////////////////// */

import UIKit
import SystemConfiguration.CaptiveNetwork
import CoreData

class LoginViewController: UIViewController {

    @IBOutlet weak var lblErrorValidator: UILabel!
    @IBOutlet weak var txtFEmail: UITextField!
    @IBOutlet weak var txtFPassword: UITextField!
    @IBOutlet weak var btnLogin: UIButton!
    @IBOutlet weak var viewLogin: UIView!
    @IBOutlet weak var imgVLogo: UIImageView!
    @IBOutlet weak var btnForgetPswd: UIButton!
    @IBOutlet weak var imgVSplashLogo: ProgressCircle!
    @IBOutlet weak var actView: UIActivityIndicatorView!
  
    var taskCDController: TasksCDController!
    var projectsCDController: ProjectsCDController!
    var moduleCDController: ModuleCDController!
    var punchInOutCDCtrlr: PunchInOutCDController!
    /// Animation time: Launch screen (Require value 3sec)
    var nSleepTime: UInt32?
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        //Set gardient color.
        self.view.addGradient()
        let cgPStart = CGPoint(x: 0, y: 0.25)
        let cgPEnd = CGPoint(x: 1, y: 0.75)
        self.btnLogin.addGradient(cgPStart: cgPStart, cgPEnd: cgPEnd, cgFRadius: 15)
        
        // Tap getsure to view
        let tapView = UITapGestureRecognizer(target: self, action: #selector(viewClickToDismiss(_:)))
        view.addGestureRecognizer(tapView)
        
        //Hide All fields during animation.
        txtFEmail.isHidden = true
        txtFPassword.isHidden = true
        btnForgetPswd.isHidden = true
        btnLogin.isHidden = true
        imgVLogo.isHidden = true
        
        taskCDController = TasksCDController()
        projectsCDController = ProjectsCDController()
        moduleCDController = ModuleCDController()
        punchInOutCDCtrlr = PunchInOutCDController()
    }
    
    /// When log in success. (Load all punch timings and perform segue to dashboard)
    private func loginSuccessHandler() {
        // Load all punch timings from server.
        // If todays date not exist.
        if !punchInOutCDCtrlr.isTodayDateExists() {
            func alertInternetConnection() {
                let alert = UIAlertController(title: "Connection Failed"
                    , message: "Please connect to internet"
                    , preferredStyle: UIAlertController.Style.alert)
                alert.addAction(UIAlertAction(title: "Retry", style: UIAlertAction.Style.default
                    , handler: {(_: UIAlertAction!) in
                        if RequestController.shared.reachabilityManager!.isReachable {
                            self.loginSuccessHandler()
                        }
                        else {
                            // Call recursively.
                            alertInternetConnection()
                        }
                }
                ))
                self.present(alert, animated: true, completion: nil)
            }
            // Check for internet connection.
            if !RequestController.shared.reachabilityManager!.isReachable {
                alertInternetConnection()
            }
            APIResponseHandler.loadPunchInOut(pageNo: g_loginPageNo, completion: {
                status in
                if status {
                    print("Successfully loaded punch timings")
                    self.performSegue(withIdentifier: "LoginSuccess", sender: nil)
                }
            })
        }
        // If already todays date exist.
        else {
            self.performSegue(withIdentifier: "LoginSuccess", sender: nil)
            APIResponseHandler.loadPunchInOut(pageNo: g_loginPageNo, completion: {
                status in
                if status {
                    print("Successfully loaded punch timings")
                }
            })
        }
    }
    
    override func viewDidAppear(_ animated: Bool) {
        super.viewDidAppear(true)
        txtFEmail.text = ""
        txtFPassword.text = ""
        //Animation time.
        sleep(nSleepTime ?? 2)
        
        //Check user login.
        if nil != UserDefaults.standard.string(forKey: "userAuthKey") {
            loginSuccessHandler()
        }
        else {
            //Show login fields.
            txtFEmail.isHidden = false
            txtFPassword.isHidden = false
            btnForgetPswd.isHidden = false
            btnLogin.isHidden = false
            imgVLogo.isHidden = false
            
            // Underline text field.
            self.txtFEmail.useUnderline()
            self.txtFPassword.useUnderline()
            
            if let email = UserDefaults.standard.value(forKey: "userEmail") {
                txtFEmail.text = (email as! String)
            }
        }
        imgVSplashLogo.isHidden = true
    }
    
    /// Calls when user clicks next key in keyboard from Email textview.
    @IBAction func txtFMoveToNextField(_ sender: Any) {
        txtFPassword.becomeFirstResponder()
    }
    
    /// Calls when user finish typing password.
    @IBAction func txtFEndPasswordEditing(_ sender: Any) {
        txtFPassword.endEditing(true)
    }
    
    @IBAction func viewPressed(_ sender: Any) {
        self.view.endEditing(true)
    }
    
    /// Method will called when user clicks on this view anywhere.
    @objc func viewClickToDismiss(_ sender: Any) {
        self.view.endEditing(true)
    }
    
    deinit {
        print("LoginViewControllers Deinitialised")
    }
    
    @IBAction func btnLoginPressed(_ sender: Any) {
        
//        txtFEmail.text = "sachin@printgreener.com"
//        txtFPassword.text = "123456"
        
        self.view.endEditing(true)
        self.lblErrorValidator.isHidden = true
        let container = NSPersistentContainer(name: "UserTaskDetails")
        print(container.persistentStoreDescriptions.first?.url as Any)
        //Email and Password Validation.
        if txtFEmail.text == "" && txtFPassword.text == "" {
            lblErrorValidator.text = "Fill all the Fields"
            lblErrorValidator.isHidden = false
            shakeTextField(textField: txtFEmail)
            shakeTextField(textField: txtFPassword)
        }
        else if !isValidEmail(strEmail: txtFEmail.text!) {
            shakeTextField(textField: txtFEmail)
            lblErrorValidator.text = "Email valid email"
            lblErrorValidator.isHidden = false
        }
        else if isValidEmail(strEmail: txtFEmail.text!) && txtFPassword.text == "" {
            lblErrorValidator.text = "Enter password"
            lblErrorValidator.isHidden = false
            shakeTextField(textField: txtFPassword)
        }
        else {
            //API validation.
            let strUserName = txtFEmail.text
            let strPswd = txtFPassword.text
            
            // If internet not connected.
            if !RequestController.shared.reachabilityManager!.isReachable {
                let alert = UIAlertController(title: "Alert", message:
                    "Your not connected to the internet.",
                                              preferredStyle: UIAlertController.Style.alert)
                alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default,
                                              handler: nil))
                self.txtFPassword.text = ""
                self.present(alert, animated: true, completion: nil)
            }
            else {
                self.actView.startAnimating()
                self.btnLogin.setTitle("", for: .normal)
                
                // Login authentication.
                APIResponseHandler.login(email: strUserName!, password: strPswd!, completion: {
                    status, msg in
                    if status {
                        UserDefaults.standard.set(strUserName, forKey: "userEmail")
                        self.loginSuccessHandler()
                        print("Log in success")
                        self.lblErrorValidator.isHidden = true
                    }
                    else {
                        print("Login Failed")
                        self.lblErrorValidator.isHidden = false
                        self.lblErrorValidator.text = msg
                        self.txtFPassword.text = ""
                    }
                    self.btnLogin.setTitle("Login", for: .normal)
                    self.actView.stopAnimating()
                })
            }
        }
    }
    
    @IBAction func btnFPasswordPressed(_ sender: Any) {
        performSegue(withIdentifier: "FindAcWithEmail", sender: nil)
    }
    
/// Returns MAC address of the connected wifi.
    public static func getWIFIInformation() -> String{
        let informationArray:NSArray? = CNCopySupportedInterfaces()
        var strMAC = ""
        if let information = informationArray {
            let dict:NSDictionary? = CNCopyCurrentNetworkInfo(information[0] as! CFString)
            if let temp = dict {
//                informationDictionary["SSID"] = String(temp["SSID"]!)
//                informationDictionary["BSSID"] = String(temp["BSSID"]!)
                strMAC = temp["BSSID"] as! String
            }
        }
        return strMAC
    }
    
    /// Shake text field.
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
    }
    
    override func traitCollectionDidChange(_ previousTraitCollection: UITraitCollection?) {
        super.traitCollectionDidChange(previousTraitCollection)
        guard UIApplication.shared.applicationState == .inactive else {
            return
        }
        
        if #available(iOS 12.0, *) {
            if self.traitCollection.userInterfaceStyle == .light {
                g_colorMode = .light
                UserDefaults.standard.setValue(1, forKey: "colorMode")
            }
            else {
                g_colorMode = .dark
                UserDefaults.standard.setValue(2, forKey: "colorMode")
            }
            setColorMode()
            updateGradient()
        }
    }
    
    /// Call when displaymode changed.
    func updateGradient() {
        // Remove old gradients.
        self.view.layer.sublayers?.removeFirst()
        self.btnLogin.layer.sublayers?.removeFirst()
        
        self.view.addGradient()
        let cgPStart = CGPoint(x: 0, y: 0.25)
        let cgPEnd = CGPoint(x: 1, y: 0.75)
        self.btnLogin.addGradient(cgPStart: cgPStart, cgPEnd: cgPEnd, cgFRadius: 15)
    }
}
