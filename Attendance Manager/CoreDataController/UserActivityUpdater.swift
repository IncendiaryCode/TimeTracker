//
//  UserActivityUpdater.swift
//  Attendance Manager
//
//  Created by Sachin on 9/17/19.
//  Copyright © 2019 Sachin. All rights reserved.
//

import Foundation
import UIKit
import CoreData

class UserActivityUpdater {
    var nsMOForUserTimes: NSManagedObject!
    var nsManagedContext: NSManagedObjectContext!
    init() {
        guard let appDelegate = UIApplication.shared.delegate as? AppDelegate else { return }
        nsManagedContext = appDelegate.persistentContainer.viewContext
    }
    
    func createNewDate() {
        let userEntity = NSEntityDescription.entity(forEntityName: "UserTimes", in:
            nsManagedContext)!
        nsMOForUserTimes = NSManagedObject(entity: userEntity, insertInto: nsManagedContext)
        nsMOForUserTimes.setValuesForKeys(["date": getDate(), "loginTime": getTimeInSec(),
                    "totalTime": 0, "workStartTime": getTimeInSec(), "isUserInBreak": false])
        do {
            try nsManagedContext.save()
        } catch let error as NSError {
            print("Could not save. \(error), \(error.userInfo)")
        }
    }
    
    func isTodayDateExists() -> Bool {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "UserTimes")
        fetchRequest.predicate = NSPredicate(format: "date == %@", getDate())
        
        let res = try! nsManagedContext.fetch(fetchRequest)
        return res.count > 0 ? true : false
    }
    
    func saveContext() {
        do {
            try nsManagedContext.save()
        } catch let error as NSError {
            print("Could not save. \(error), \(error.userInfo)")
        }
    }
    
    func updateUSerWorkTime() {
        //Updates userwork time.
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "UserTimes")
        fetchRequest.predicate = NSPredicate(format: "date = %@", getDate())
        
        do {
            let test = try nsManagedContext.fetch(fetchRequest)
            let objectUpdate = test[0] as! NSManagedObject
            let nPrevTime = objectUpdate.value(forKey: "workStartTime") as! Int
            if !(IsUserInBreak()) {
                let prevTotalWork = objectUpdate.value(forKey: "totalTime") as! Int
                let totalWork = prevTotalWork + getTimeInSec() - nPrevTime
                objectUpdate.setValue(totalWork, forKey: "totalTime")
                objectUpdate.setValue(getTimeInSec(), forKey: "workStartTime")
                saveContext()
            }
        }
        catch
        {
            print(error)
        }
    }
    
    func fetchAllData() {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "UserTimes")
        do {
            let result = try nsManagedContext.fetch(fetchRequest)
            print(result.count)
            for data in result as! [NSManagedObject] {
                print("Date: \(data.value(forKey: "date") as! String)")
                print(
    "Log In: \(getSecondsToHoursMinutesSeconds(seconds: data.value(forKey: "loginTime") as! Int))")
                print(
    "Total Time: \(getSecondsToHoursMinutesSeconds(seconds: data.value(forKey: "totalTime") as! Int))")
                print(
    "Started at: \(getSecondsToHoursMinutesSeconds(seconds:data.value(forKey: "workStartTime") as! Int))")
                print(
    "Logout: \(getSecondsToHoursMinutesSeconds(seconds:data.value(forKey: "logoutTime") as! Int))")
                print(
    "In Break: \(data.value(forKey: "isUserInBreak") as! Bool)")
            }
        } catch {
            print("Failed")
        }
    }
    
    func IsUserTerminatedWork() -> Bool {
        //Is user terminated work of that particular day.
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "UserTimes")
        fetchRequest.predicate = NSPredicate(format: "date = %@", getDate())
        var bIsLoggedIn: Bool!
        do {
            let test = try nsManagedContext.fetch(fetchRequest)
            let objectUpdate = test[0] as! NSManagedObject
            if objectUpdate.value(forKey: "logoutTime") as! Int == 0 {
                bIsLoggedIn = false
            }
            else {
                bIsLoggedIn = true
            }
        }
        catch {
            print("Error")
        }
        return bIsLoggedIn
    }
    
    func getLoginTime() -> Int {
        //Return total work time in second.
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "UserTimes")
        fetchRequest.predicate = NSPredicate(format: "date = %@", getDate())
        var nLoginTime: Int!
        do {
            let test = try nsManagedContext.fetch(fetchRequest)
            let objectUpdate = test[0] as! NSManagedObject
            nLoginTime = objectUpdate.value(forKey: "loginTime") as? Int
        }
        catch {
            print("Error")
        }
        return nLoginTime
    }
    
    func deleteAllData() {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "UserTimes")
        fetchRequest.returnsObjectsAsFaults = false
        do {
            let results = try nsManagedContext.fetch(fetchRequest)
            for object in results {
                guard let objectData = object as? NSManagedObject else {continue}
                nsManagedContext.delete(objectData)
                saveContext()
            }
        }
        catch {
            print("Error")
        }
    }
    
    func IsUserInBreak() -> Bool {
        //Returns whether user in break or not.
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "UserTimes")
        fetchRequest.predicate = NSPredicate(format: "date = %@", getDate())
        var bIsInBreak: Bool!
        do {
            let test = try nsManagedContext.fetch(fetchRequest)
            let objectUpdate = test[0] as! NSManagedObject
            if objectUpdate.value(forKey: "isUserInBreak") as! Bool == false {
                bIsInBreak = false
            }
            else {
                bIsInBreak = true
            }
        }
        catch {
            print("Error")
        }
        return bIsInBreak
    }
    
    func getTotalTime() -> Int {
        //Returns total time in seconds.
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "UserTimes")
        fetchRequest.predicate = NSPredicate(format: "date = %@", getDate())
        var nTotalTime: Int!
        do {
            let test = try nsManagedContext.fetch(fetchRequest)
            let objectUpdate = test[0] as! NSManagedObject
            nTotalTime = objectUpdate.value(forKey: "totalTime") as? Int
        }
        catch {
            print("Error")
        }
        return nTotalTime
    }
    
    func userStartsBreak() {
        //When user starts break, update timings.
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "UserTimes")
        fetchRequest.predicate = NSPredicate(format: "date = %@", getDate())
        
        do {
            let test = try nsManagedContext.fetch(fetchRequest)
            let objectUpdate = test[0] as! NSManagedObject
            let nPrevTime = objectUpdate.value(forKey: "workStartTime") as! Int
            let prevTotalWork = objectUpdate.value(forKey: "totalTime") as! Int
            let totalWork = prevTotalWork + getTimeInSec() - nPrevTime
            objectUpdate.setValue(totalWork, forKey: "totalTime")
            objectUpdate.setValue(true, forKey: "isUserInBreak")
            saveContext()
        }
        catch
        {
            print(error)
        }
    }
    
    func userFinishedBreak() {
        //User finishesh break, updates time.
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "UserTimes")
        fetchRequest.predicate = NSPredicate(format: "date = %@", getDate())
        
        do {
            let test = try nsManagedContext.fetch(fetchRequest)
            let objectUpdate = test[0] as! NSManagedObject
            
            objectUpdate.setValue(false, forKey: "isUserInBreak")
            objectUpdate.setValue(getTimeInSec(), forKey: "workStartTime")
            saveContext()
        }
        catch
        {
            print(error)
        }
    }
    
    func userLoggedOut() {
        //When user logsout.
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "UserTimes")
        fetchRequest.predicate = NSPredicate(format: "date = %@", getDate())
        
        do {
            let test = try nsManagedContext.fetch(fetchRequest)
            let objectUpdate = test[0] as! NSManagedObject
            
            objectUpdate.setValue(getTimeInSec(), forKey: "logoutTime")
            saveContext()
        }
        catch
        {
            print(error)
        }
    }
}
