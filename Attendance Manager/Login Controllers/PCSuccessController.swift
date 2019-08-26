//
//  PCSuccessController.swift
//  Attendance Manager
//
//  Created by Sachin on 9/4/19.
//  Copyright © 2019 Sachin. All rights reserved.
//

import UIKit

class PCSuccessController: UIViewController {

    @IBOutlet weak var viewSuccess: UIView!
    @IBOutlet weak var btnLogin: UIButton!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.view.addGradient(startColor: cgCForGradientStart, endColor: cgCForGradientEnd)
        self.viewSuccess.addGradient(startColor: cgCForGradientStart,
                                     endColor: cgCForGradientEnd, cgFRadius: 50)
        self.btnLogin.addGradient(startColor: cgCForGradientStart,
                                  endColor: cgCForGradientEnd, cgFRadius: 15)
        // Do any additional setup after loading the view.
    }
    
    @IBAction func btnContinuePressed(_ sender: Any) {
        self.view.window!.rootViewController?.dismiss(animated: false, completion: nil)
    }
    
    /*
    // MARK: - Navigation

    // In a storyboard-based application, you will often want to do a little preparation before
     navigation
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        // Get the new view controller using segue.destination.
        // Pass the selected object to the new view controller.
    }
    */

}
