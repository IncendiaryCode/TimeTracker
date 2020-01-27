/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : TabBarController.swift
 //
 //    File Created      : 26:Dec:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : Tab-bar Controller.
 //
 //////////////////////////////////////////////////////////////////////////// */

import UIKit

class TabBarController: UITabBarController, UITabBarControllerDelegate {

    override func viewDidLoad() {
        super.viewDidLoad()
        
        // Set default selection to dashboard.
        self.selectedIndex = 1
        self.delegate = self
        tabBar.backgroundColor = g_colorMode.defaultColor()
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(true)
//        let _ = self.viewControllers?[0].view // Remove in future.
    }
    
    func tabBarController(_ tabBarController: UITabBarController, didSelect
        viewController: UIViewController) {
        
        let selectedIndex = tabBarController.viewControllers?.firstIndex(of: viewController)!
        if selectedIndex == 0 {
            // When Tasks Bar selected update user time task timings.
            let tasksCDController = TasksCDController()
            tasksCDController.updateUserTaskTime()
        }
        else if selectedIndex == 1 {
//            let userActVC = tabBarController.viewControllers?[1] as! UserActivityVC
            let tasksCDController = TasksCDController()
            tasksCDController.updateUserTaskTime()
            //userActVC.sortAndRefreshData()
        }
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
            updateAllViewCtrlrs()
        }
    }
    
    func updateAllViewCtrlrs() {
        // Get all view cntrlr objects.
        let tabBarCtrlr = self.navigationController?.viewControllers[0] as! TabBarController
        tabBar.backgroundColor = g_colorMode.defaultColor()
        
        let taskHistoryVC = tabBarCtrlr.viewControllers?[0] as! MyActivityViewController
        let userActVC = tabBarCtrlr.viewControllers?[1] as! UserActivityVC
        let accountVC = tabBarCtrlr.viewControllers?[2] as! AccountViewController

        // Remove all gradient layers from view.
        taskHistoryVC.view.layer.sublayers?.removeFirst()
        
        userActVC.view.layer.sublayers?.removeFirst()
        userActVC.btnState.layer.sublayers?.removeFirst()
        userActVC.btnFinish.layer.sublayers?.removeFirst()
        
        accountVC.view.layer.sublayers?.removeFirst()
        
        setColorMode()
                
        // setup all day, week and monthly view.
        if nil != taskHistoryVC.arrActView {
            // Set date graph view to front.
            taskHistoryVC.viewSelection.removeFromSuperview()
            taskHistoryVC.view.bringSubviewToFront(taskHistoryVC.arrActView[0])
            for actView in taskHistoryVC.arrActView {
                actView.removeFromSuperview()
                actView.delegate = nil
                actView.delegateChart = nil
                actView.calendarView.delegateChart = nil
                actView.calendarView.delegate = nil
            }
            taskHistoryVC.arrActView = nil
        }
        // Update colors to all view controllers view.
        taskHistoryVC.updateViewsAndColor()
        taskHistoryVC.setUpViews()
        userActVC.updateViewsAndColor()
        accountVC.updateViewsAndColor()
    }
}
