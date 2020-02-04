/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : AboutAppVC.swift
 //
 //    File Created      : 14:Jan:2020
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : About page of app.
 //
 //////////////////////////////////////////////////////////////////////////// */

import UIKit

class AboutAppVC: UIViewController {
    
    @IBOutlet weak var btnBack: UIButton!
    @IBOutlet weak var imgLogo: UIImageView!
    @IBOutlet weak var viewContainer: UIView!
    @IBOutlet weak var scrollView: UIScrollView!
    @IBOutlet weak var lblInfo: UILabel!
    @IBOutlet weak var viewFooter: UIView!
    @IBOutlet weak var lblAbout: UILabel!
    @IBOutlet weak var lblVersion: UILabel!
    @IBOutlet weak var lblCopyright: UILabel!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        view.addGradient(cgPStart: CGPoint(x: 0, y: 0), cgPEnd: CGPoint(x: 1, y: 0.3))
        // Setting navigation swipe recognizer.
        self.navigationController?.interactivePopGestureRecognizer?.delegate = nil
        self.navigationController?.interactivePopGestureRecognizer?.isEnabled = true
        scrollView.layer.masksToBounds = true
        scrollView.layer.cornerRadius = 35
        scrollView.backgroundColor = g_colorMode.defaultColor()
        scrollView.layer.borderColor = g_colorMode.lineColor().cgColor
        scrollView.layer.borderWidth = 0.3
        imgLogo.image = #imageLiteral(resourceName: "ProjectLogo2")
        
        // Get app version
        if let version = Bundle.main.infoDictionary?["CFBundleShortVersionString"] as? String {
            self.lblVersion.text = "Version \(version)"
            if let build = Bundle.main.infoDictionary?["CFBundleVersion"] as? String {
                self.lblVersion.text = "\(self.lblVersion.text!).\(build)"
            }
        }
    }
    
    @IBAction func btnBackPressed(_ sender: Any) {
        self.navigationController?.popToRootViewController(animated: true)
    }
}
