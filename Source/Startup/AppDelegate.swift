/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : AppDelegate.swift
 //
 //    File Created      : 29:Aug:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : App Delegate.
 //
 //////////////////////////////////////////////////////////////////////////// */

import UIKit
import CoreData

@UIApplicationMain
class AppDelegate: UIResponder, UIApplicationDelegate, UNUserNotificationCenterDelegate {
    
    var window: UIWindow?
    var taskUpdater: TasksCDController!
    var userTimeUpdater: PunchInOutCDController!
    
    func application(_ application: UIApplication, didFinishLaunchingWithOptions launchOptions:
        [UIApplication.LaunchOptionsKey: Any]?) -> Bool {
        // Set color mode.
        setAppConfig()
        setColorMode()
        /// This will notify whenever internet connection status changes.
        RequestController.shared.startNetworkReachabilityObserver()
        
        // Ask permission for notifications
        let center = UNUserNotificationCenter.current()
        center.delegate = self
        center.requestAuthorization(options: [.alert, .badge, .sound]) { (granted, error) in
            if granted {
                self.setUpNotification(hour: 10, minute: 00, title: "Reminder"
                    , body: "Good morning, Reminder to punch in for the day")
                self.setUpNotification(hour: 17, minute: 55, title: "Reminder"
                    , body: "Good evening, Reminder to punch out for the day")
            } else {
                print("Permission denied to notifications")
            }
        }
        return true
    }
    
    func application(_ application: UIApplication, performActionFor
        shortcutItem:UIApplicationShortcutItem, completionHandler: @escaping (Bool) -> Void) {
        // Check for shortcut and userlogin.
        if shortcutItem.type == "taskAdd" &&
                nil != UserDefaults.standard.string(forKey: "userAuthKey") {
            let projectCDCtrlr = ProjectsCDController()
            g_dictProjectDetails = projectCDCtrlr.getAllProjectDetails()
            
            let mainStoryboardIpad : UIStoryboard = UIStoryboard(name: "Main", bundle: nil)
            let initialViewController = mainStoryboardIpad.instantiateViewController(withIdentifier:
                "TaskViewController") as! TaskViewController
            initialViewController.bIsQuickAction = true
            self.window = UIWindow(frame: UIScreen.main.bounds)
            self.window?.rootViewController = initialViewController
            self.window?.makeKeyAndVisible()
        }
    }
    
    func applicationWillResignActive(_ application: UIApplication) {
        if nil != UserDefaults.standard.string(forKey: "userAuthKey") {
            taskUpdater = TasksCDController()
            taskUpdater.updateUserTaskTime()
            userTimeUpdater = PunchInOutCDController()
        }
        // Sent when the application is about to move from active to inactive state. This can occur for certain types of temporary interruptions (such as an incoming phone call or SMS message) or when the user quits the application and it begins the transition to the background state.
        // Use this method to pause ongoing tasks, disable timers, and invalidate graphics rendering callbacks. Games should use this method to pause the game.
    }
    
    func applicationDidEnterBackground(_ application: UIApplication) {
        if nil != UserDefaults.standard.string(forKey: "userAuthKey") {
            taskUpdater = TasksCDController()
            taskUpdater.updateUserTaskTime()
            userTimeUpdater = PunchInOutCDController()
        }
        // Use this method to release shared resources, save user data, invalidate timers, and store enough application state information to restore your application to its current state in case it is terminated later.
        // If your application supports background execution, this method is called instead of applicationWillTerminate: when the user quits.
    }
    
    func applicationWillEnterForeground(_ application: UIApplication) {
        
        // Called as part of the transition from the background to the active state; here you can undo many of the changes made on entering the background.
    }
    
    func applicationDidBecomeActive(_ application: UIApplication) {
        // Restart any tasks that were paused (or not yet started) while the application was inactive. If the application was previously in the background, optionally refresh the user interface.// Is there a shortcut item that has not yet been processed?
            // In this sample an alert is being shown to indicate that the action has been triggered,
            // but in real code the functionality for the quick action would be triggered.
    }
    
    func applicationWillTerminate(_ application: UIApplication) {
        if nil != UserDefaults.standard.string(forKey: "userAuthKey") {
            taskUpdater = TasksCDController()
            taskUpdater.updateUserTaskTime()
            userTimeUpdater = PunchInOutCDController()
        }
        // Called when the application is about to terminate. Save data if appropriate. See also applicationDidEnterBackground:.
        // Saves changes in the application's managed object context before the application terminates.
        self.saveContext()
    }
    
    /// Setup notification.
    func setUpNotification(hour: Int, minute: Int, title: String, body: String) {
        let calendar = Calendar.current
        var dateFire = Date()
        
        // if today's date is passed, use tomorrow
        var fireComponents = calendar.dateComponents( [Calendar.Component.day
            , Calendar.Component.month, Calendar.Component.year, Calendar.Component.hour
            , Calendar.Component.minute], from:dateFire)
        
        if (fireComponents.hour! > hour
            || (fireComponents.hour == hour && fireComponents.minute! >= minute) ) {
            
            dateFire = dateFire.addingTimeInterval(86400)  // Use tomorrow's date
            fireComponents = calendar.dateComponents( [Calendar.Component.day
                , Calendar.Component.month, Calendar.Component.year, Calendar.Component.hour
                , Calendar.Component.minute], from:dateFire)
        }
        
        // set up the time
        fireComponents.hour = hour
        fireComponents.minute = minute
        fireComponents.second = 0
        
        // Check for weekends.(1 for sunday and 7 for saturday)
        guard fireComponents.weekday != 1 || fireComponents.weekday != 7 else {
            return
        }
        
        // schedule local notification
        dateFire = calendar.date(from: fireComponents)!
        let center = UNUserNotificationCenter.current()
        let content = UNMutableNotificationContent()
        content.title = title
        content.body = body
        content.sound = UNNotificationSound.default

        let trigger = UNCalendarNotificationTrigger(dateMatching: fireComponents, repeats: true)
        
        // Create request
        let uniqueID = "time:\(hour)\(minute)" // Keep a record of this to avoid duplicate requests.
        let request = UNNotificationRequest(identifier: uniqueID, content: content, trigger: trigger)
        center.add(request) // Add the notification request
    }
    
    // MARK: - Core Data stack
    
    lazy var persistentContainer: NSPersistentContainer = {
        /*
         The persistent container for the application. This implementation
         creates and returns a container, having loaded the store for the
         application to it. This property is optional since there are legitimate
         error conditions that could cause the creation of the store to fail.
         */
        let container = NSPersistentContainer(name: "UserTaskDetails")
        container.loadPersistentStores(completionHandler: { (storeDescription, error) in
            if let error = error as NSError? {
                // Replace this implementation with code to handle the error appropriately.
                // fatalError() causes the application to generate a crash log and terminate. You should not use this function in a shipping application, although it may be useful during development.
                
                /*
                 Typical reasons for an error here include:
                 * The parent directory does not exist, cannot be created, or disallows writing.
                 * The persistent store is not accessible, due to permissions or data protection when the device is locked.
                 * The device is out of space.
                 * The store could not be migrated to the current model version.
                 Check the error message to determine what the actual problem was.
                 */
                fatalError("Unresolved error \(error), \(error.userInfo)")
            }
        })
        return container
    }()
    
    // MARK: - Core Data Saving support
    
    func saveContext () {
        let context = persistentContainer.viewContext
        if context.hasChanges {
            do {
                try context.save()
            } catch {
                // Replace this implementation with code to handle the error appropriately.
                // fatalError() causes the application to generate a crash log and terminate. You should not use this function in a shipping application, although it may be useful during development.
                let nserror = error as NSError
                fatalError("Unresolved error \(nserror), \(nserror.userInfo)")
            }
        }
    }
    
}
