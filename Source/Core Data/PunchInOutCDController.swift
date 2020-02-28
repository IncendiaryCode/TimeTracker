 /*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : PunchInOutCDController.swift
 //
 //    File Created      : 17:Sept:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : Punch_in_out_time entity Core data hndler.
 //
 //////////////////////////////////////////////////////////////////////////// */

import Foundation
import UIKit
import CoreData

class PunchInOutCDController {
    var nsMOForUserTimes: NSManagedObject!
    var nsManagedContext: NSManagedObjectContext!
    init() {
        guard let appDelegate = UIApplication.shared.delegate as? AppDelegate else { return }
        nsManagedContext = appDelegate.persistentContainer.viewContext
    }
    
    /// Creates new date for punch in.
    func createNewDate() {
        // If new date stop running task.
//        let taskCDCtrlr = TasksCDController()
//        taskCDCtrlr.stopRunningTask()
//
//        let userEntity = NSEntityDescription.entity(forEntityName: "Punch_in_out_time", in:
//            nsManagedContext)!
//        nsMOForUserTimes = NSManagedObject(entity: userEntity, insertInto: nsManagedContext)
//
//        nsMOForUserTimes.setValuesForKeys(["punch_in_time": Date().millisecondsSince1970])
//        saveContext()
    }
    
    /// Commits to core data.
    func saveContext() {
        do {
            try nsManagedContext.save()
        } catch let error as NSError {
            print("Could not save. \(error), \(error.userInfo)")
        }
    }
    
    /// Returns previous date .
    func getPreviousDate() -> Date {
        var date: Date!
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Punch_in_out_time")
        fetchRequest.sortDescriptors = [NSSortDescriptor(key: "punch_in_time", ascending: false)]
        do {
            let nsMContext = try nsManagedContext.fetch(fetchRequest)
            if nsMContext.count > 0 {
                let value = nsMContext[0] as! NSManagedObject
                let intDate = (value.value(forKey: "punch_in_time") as! Int64)
                date = Date(milliseconds: (intDate - Int64(TimeZone.current.secondsFromGMT())))
            }
            
        }
        catch {
            print("Error")
        }
        return date
    }
    
    /// Check previous day's punch out time updated or not.
    func isPreviousDayUpdated() -> Bool {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Punch_in_out_time")
        fetchRequest.sortDescriptors = [NSSortDescriptor(key: "punch_in_time", ascending: false)]
        do {
            let nsMContext = try nsManagedContext.fetch(fetchRequest)
            if nsMContext.count > 0 {
                let value = nsMContext[0] as! NSManagedObject
                let date = Date(milliseconds: (value.value(forKey: "punch_in_time") as! Int64) - Int64(TimeZone.current.secondsFromGMT()))
                let strDate = getStrDateTime(date: date)
                if nil != value.value(forKey: "punch_out_time") || strDate == getCurrentDate() {
                    return true
                }
                else {
                    return false
                }
            }
            else {
                return true
            }
        }
        catch {
            return false
        }
    }
    
    /// get exact punch in time from any time in that date
    func getPunchInTimeFrom(date: Int64) -> Int64? {
        var intDate: Int64?
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Punch_in_out_time")
        fetchRequest.sortDescriptors = [NSSortDescriptor(key: "punch_in_time", ascending: true)]
        do {
            let nsMContext = try nsManagedContext.fetch(fetchRequest)
            for i in 0..<nsMContext.count {
                let nsMObject = nsMContext[i] as! NSManagedObject
                let punchInDateInCD = nsMObject.value(forKey: "punch_in_time") as! Int64
                if punchInDateInCD >= date {
                    let value = nsMContext[i] as! NSManagedObject
                    intDate = (value.value(forKey: "punch_in_time") as! Int64)
                    break
                }
            }
        }
        catch {
            print("Error")
        }
        return intDate
    }
    
    /// get exact punch in of today.
    func getPunchInTime() -> Date {
        var date: Date!
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Punch_in_out_time")
        fetchRequest.sortDescriptors = [NSSortDescriptor(key: "punch_in_time", ascending: false)]
        do {
            let nsMContext = try nsManagedContext.fetch(fetchRequest)
            if nsMContext.count > 0{
                let nsMObject = nsMContext[0] as! NSManagedObject
                let nTime = nsMObject.value(forKey: "punch_in_time") as! Int64
                date = Date(milliseconds: (nTime - Int64(TimeZone.current.secondsFromGMT())))
            }
        }
        catch {
            print("Error")
        }
        return date
    }
    
    /// get  punch in and out of recent punch.
    func getPunchInAndOutTime() -> (Date, Date?) {
        var datePunchIn: Date!
        var datePunchOut: Date?
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Punch_in_out_time")
        fetchRequest.sortDescriptors = [NSSortDescriptor(key: "punch_in_time", ascending: false)]
        do {
            let nsMContext = try nsManagedContext.fetch(fetchRequest)
            if nsMContext.count > 0{
                let nsMObject = nsMContext[0] as! NSManagedObject
                let nStartTime = nsMObject.value(forKey: "punch_in_time") as! Int64
                datePunchIn = Date(milliseconds:
                    (nStartTime - Int64(TimeZone.current.secondsFromGMT())))
                if let nEndTime = nsMObject.value(forKey: "punch_out_time") as? Int64 {
                    datePunchOut = Date(milliseconds:
                        (nEndTime - Int64(TimeZone.current.secondsFromGMT())))
                }
            }
        }
        catch {
            print("Error")
        }
        return (datePunchIn, datePunchOut)
    }
    
    /// To load sample data.
    func addOrUpdatePunchInOutTime(start: Int64, end: Int64?) {
        if !isPunchInExist(punchInTime: start) {
            let userEntity = NSEntityDescription.entity(forEntityName: "Punch_in_out_time",
                                                        in: nsManagedContext)!
            nsMOForUserTimes = NSManagedObject(entity: userEntity, insertInto: nsManagedContext)
            if let endTime = end {
                nsMOForUserTimes.setValuesForKeys(["punch_in_time": start, "punch_out_time": endTime])
            }
            else {
                nsMOForUserTimes.setValuesForKeys(["punch_in_time": start])
            }
        }
        else {
            let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Punch_in_out_time")
            fetchRequest.predicate = NSPredicate(format: "punch_in_time = %d", start)
            let res = try! nsManagedContext.fetch(fetchRequest)
            if res.count > 0 {
                let nsMObject = res[0] as! NSManagedObject
                nsMObject.setValue(end, forKey: "punch_out_time")
            }
        }
        saveContext()
    }
    
    func isPunchInExist(punchInTime: Int64) -> Bool {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Punch_in_out_time")
        fetchRequest.predicate = NSPredicate(format: "punch_in_time = %d", punchInTime)
        let res = try! nsManagedContext.fetch(fetchRequest)
        return res.count > 0 ? true : false
    }
    
    /// returns next date of provided date.
    func getFutureDate(date: Int64) -> Int64? {
        var intDate: Int64?
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Punch_in_out_time")
        fetchRequest.sortDescriptors = [NSSortDescriptor(key: "punch_in_time", ascending: false)]
        do {
            let nsMContext = try nsManagedContext.fetch(fetchRequest)
            for i in 0..<nsMContext.count {
                let nsMObject = nsMContext[i] as! NSManagedObject
                // Compare every punch in date with sent date.
                let dateInCD = nsMObject.value(forKey: "punch_in_time") as! Int64
                if dateInCD == date && i != 0 {
                    let value = nsMContext[i-1] as! NSManagedObject
                    intDate = (value.value(forKey: "punch_in_time") as! Int64)
                    break
                }
            }
        }
        catch {
            print("Error")
        }
        return intDate
    }
    
    /// Checks todays date is exists in Punch_in_out_time or not.
    func isTodayDateExists() -> Bool {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Punch_in_out_time")
        fetchRequest.sortDescriptors = [NSSortDescriptor(key: "punch_in_time", ascending: false)]
        do {
            let nsMContext = try nsManagedContext.fetch(fetchRequest)
            if nsMContext.count > 0 {
                let nsMObject = nsMContext[0] as! NSManagedObject
                let date = Date(milliseconds: nsMObject.value(forKey: "punch_in_time") as! Int64 - Int64(TimeZone.current.secondsFromGMT()))
                let strDate = getStrDateTime(date: date)
                if strDate == getCurrentDate() {
                    g_isPunchedIn = true
                    return true
                }
                else {
                    // Extend checking.(Check all available dates.)
                    for i in 1..<nsMContext.count {
                        let nsMObject = nsMContext[i] as! NSManagedObject
                        let date = Date(milliseconds: nsMObject.value(forKey: "punch_in_time")
                            as! Int64 - Int64(TimeZone.current.secondsFromGMT()))
                        let strDate = getStrDateTime(date: date)
                        if strDate == getCurrentDate() {
                            g_isPunchedIn = true
                            return true
                        }
                    }
                }
            }
        }
        catch {
            print("Error while fetching..!")
        }
        g_isPunchedIn = false
        return false
    }
    
    /// To check sent time is in between punch in and out.
    func isTimeExistInPunchInOut(start: Date, end: Date? = nil) -> (Bool, String?, String?) {
        let nStart = start.millisecondsSince1970+Int64(TimeZone.current.secondsFromGMT())
        var nEnd: Int64?
        if nil != end {
            nEnd = end!.millisecondsSince1970+Int64(TimeZone.current.secondsFromGMT())
        }
        
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Punch_in_out_time")
        fetchRequest.sortDescriptors = [NSSortDescriptor(key: "punch_in_time", ascending: false)]
        do {
            let nsMContext = try nsManagedContext.fetch(fetchRequest)
            for i in 0..<nsMContext.count {
                let nsMObject = nsMContext[i] as! NSManagedObject
                let nPunchIn = nsMObject.value(forKey: "punch_in_time") as! Int64
                let nStartTime = nsMObject.value(forKey: "punch_in_time") as! Int64
                let datePunchIn = Date(milliseconds:
                    (nStartTime - Int64(TimeZone.current.secondsFromGMT())))
                
                // If date is today check only punch in.
                let strDate = getStrDateTime(date: start)
                if strDate == getCurrentDate()
                    && nil == nsMObject.value(forKey: "punch_out_time") as? Int64 {
                    if nPunchIn <= nStart {
                        return (true, nil, nil)
                    }
                    else {
                        return (false, datePunchIn.getStrTime(), nil)
                    }
                }
                
                let punchInDate = datePunchIn.getStrDate()
                // Check other day.
                if let nPunchOut = nsMObject.value(forKey: "punch_out_time") as? Int64
                    , getStrDateTime(date: start) == punchInDate {
                    let datePunchOut = Date(milliseconds:
                        (nPunchOut - Int64(TimeZone.current.secondsFromGMT())))
                    if nil == nEnd && nPunchIn <= nStart && nPunchOut >= nStart {
                        return (true, nil, nil)
                    }
                    else if nPunchIn <= nStart && nPunchOut >= nEnd! {
                        return (true, nil, nil)
                    }
                    else {
                        return (false, datePunchIn.getStrTime(), datePunchOut.getStrTime())
                    }
                }
            }
            return (false, nil, nil)
        }
        catch {
            print("Error while fetching..!")
            return (false, nil, nil)
        }
    }
    
    /// Check punch out updated or not.
    func isTodayPunchedOut() -> Bool {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Punch_in_out_time")
        fetchRequest.sortDescriptors = [NSSortDescriptor(key: "punch_in_time", ascending: false)]
        do {
            let nsMContext = try nsManagedContext.fetch(fetchRequest)
            if nsMContext.count > 0 {
                let nsMObject = nsMContext[0] as! NSManagedObject
                let date = Date(milliseconds: nsMObject.value(forKey: "punch_in_time") as! Int64 - Int64(TimeZone.current.secondsFromGMT()))
                let strDate = getStrDateTime(date: date)
                let punchOut = nsMObject.value(forKey: "punch_out_time") as? Int64
                if nil != punchOut && strDate == getCurrentDate() {
                    return true
                }
            }
        }
        catch {
            print("Error while fetching..!")
        }
        return false
    }
        
    /// To get current date login time.
    func getLoginTime() -> Int {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Punch_in_out_time")
        fetchRequest.sortDescriptors = [NSSortDescriptor(key: "punch_in_time", ascending: false)]
        var nLoginTime: Int = 0
        do {
            let nsMContext = try nsManagedContext.fetch(fetchRequest)
            let nsMObject = nsMContext[0] as! NSManagedObject
            let nTime = nsMObject.value(forKey: "punch_in_time") as! Int64
            let date = Date(milliseconds: nTime) // Convert to date object.
            nLoginTime = date.timeInDate // Convert to time in seconds.
        }
        catch {
            print("Error")
        }
        return nLoginTime
    }
    
    /// to get login time and date as integer.
    func getLoginDateTime() -> Int64 {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Punch_in_out_time")
        fetchRequest.sortDescriptors = [NSSortDescriptor(key: "punch_in_time", ascending: false)]
        var nLoginTime: Int64 = 0
        do {
            let nsMContext = try nsManagedContext.fetch(fetchRequest)
            let nsMObject = nsMContext[0] as! NSManagedObject
            nLoginTime = nsMObject.value(forKey: "punch_in_time") as! Int64
        }
        catch {
            print("Error")
        }
        return nLoginTime
    }
    
    /// to get login time and date as integer.
    func getLogoutDateTime() -> Int64 {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Punch_in_out_time")
        fetchRequest.sortDescriptors = [NSSortDescriptor(key: "punch_in_time", ascending: false)]
        var nLogoutTime: Int64 = 0
        do {
            let nsMContext = try nsManagedContext.fetch(fetchRequest)
            let nsMObject = nsMContext[0] as! NSManagedObject
            if let nTime = nsMObject.value(forKey: "punch_out_time") as? Int64 {
                nLogoutTime = nTime
            }
        }
        catch {
            print("Error")
        }
        return nLogoutTime
    }
    
    /// Deletes entire 'UserTimes' entity data.
    func deleteAllData() {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Punch_in_out_time")
        fetchRequest.returnsObjectsAsFaults = false
        do {
            let nsMContext = try nsManagedContext.fetch(fetchRequest)
            for object in nsMContext {
                guard let nsMObject = object as? NSManagedObject else {continue}
                nsManagedContext.delete(nsMObject)
                saveContext()
            }
        }
        catch {
            print("Error")
        }
    }
    
    /// Returns current date, total time in seconds.
    func getTotalTime() -> Int {
        let nLoginTime = getLoginDateTime()
        let nLogout = getLogoutDateTime()
        
        // If logged out.
        if nLogout != 0 {
            return Int((nLogout - nLoginTime))
        }
        let currentTime = Date().millisecondsSince1970 + Int64(TimeZone.current.secondsFromGMT())
        return Int((currentTime - nLoginTime))
    }
}
