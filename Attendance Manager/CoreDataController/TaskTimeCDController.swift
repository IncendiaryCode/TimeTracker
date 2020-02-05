/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : TasksTimeCDController.swift
 //
 //    File Created      : 14:Oct:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : Tasks_time entity Core data hndler.
 //
 //////////////////////////////////////////////////////////////////////////// */

import Foundation
import UIKit
import CoreData

class TasksTimeCDController {
    var nsMOForUserTimes: NSManagedObject!
    var nsManagedContext: NSManagedObjectContext!
    
    init() {
        guard let appDelegate = UIApplication.shared.delegate as? AppDelegate else { return }
        nsManagedContext = appDelegate.persistentContainer.viewContext
    }
    
    /// commit database.
    func saveContext() {
        do {
            try nsManagedContext.save()
        } catch let error as NSError {
            print("Could not save. \(error), \(error.userInfo)")
        }
    }
    
    /// Add Work Start Time.
    func addStartTime(taskId: Int) {
        // get time id.
        let timeId = getTimeIdToAddNewTaskTime()
        let userEntity = NSEntityDescription.entity(forEntityName: "Tasks_time",
                                                    in: nsManagedContext)!
        nsMOForUserTimes = NSManagedObject(entity: userEntity, insertInto: nsManagedContext)
        nsMOForUserTimes.setValuesForKeys(["time_id": timeId, "task_id": taskId,
                                           "date": getDate(), "start_time": getTimeInSec()])
        saveContext()
    }
    
    /// Add new task timings.
    func addOrUpdateTaskTimings(timeId: Int, taskId: Int, strDate: String, startTime: Int,
                                endTime: Int, descr: String?) {
        if !isTimeIdExist(timeId: timeId) {
            // Creates new task time.
            let userEntity = NSEntityDescription.entity(forEntityName: "Tasks_time",
                                                        in: nsManagedContext)!
            nsMOForUserTimes = NSManagedObject(entity: userEntity, insertInto: nsManagedContext)
            nsMOForUserTimes.setValuesForKeys(["time_id": timeId, "task_id": taskId,                                            "date": strDate, "start_time": startTime, "end_time": endTime
                , "task_description": descr ?? ""])
        }
        else {
            // Updates existing timeid timings.
            updateDateTimings(timeId: timeId, date: strDate, startTime: startTime, endTime: endTime,
                              descr: descr ?? "")
        }
        saveContext()
    }
    
    /// Add data to tasks_time.
    func addTaskTimings(timeId: Int, taskId: Int, strDate: String, startTime: Int,
                        endTime: Int, desc: String) {
        let userEntity = NSEntityDescription.entity(forEntityName: "Tasks_time",
                                                    in: nsManagedContext)!
        nsMOForUserTimes = NSManagedObject(entity: userEntity, insertInto: nsManagedContext)
        
        nsMOForUserTimes.setValuesForKeys(["time_id": timeId, "task_id": taskId, "date": strDate,
            "start_time": startTime, "end_time": endTime, "task_description": desc])
        saveContext()
    }
    
    /// To clear all task timings.
    func clearTaskTimings(taskId: Int) {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
        fetchRequest.predicate = NSPredicate(format: "date == %@", getDate())
        let nsMContexts = try! nsManagedContext.fetch(fetchRequest)
        if nsMContexts.count > 0 {
            for nsMObject in nsMContexts {
                nsManagedContext.delete(nsMObject as! NSManagedObject)
            }
        }
        saveContext()
    }
    
    /// Returns new time id for creating new row.
    func getTimeIdToAddNewTaskTime() -> Int {
        var timeId: Int!
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
        do {
            timeId = try nsManagedContext.count(for: fetchRequest)
            // If already data exist then, increment previous time id by one.
            if timeId != 1 {
                let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
                fetchRequest.sortDescriptors = [NSSortDescriptor(key: "time_id", ascending: true)]
                let res = try! nsManagedContext.fetch(fetchRequest)
                if res.count > 0 {
                    let nsMObject = res[0] as! NSManagedObject
                    // Update previos time id by one.
                    
                    let firstTimeId = nsMObject.value(forKey: "time_id") as! Int
                    // If it is not negative value.
                    if firstTimeId > 0 {
                        timeId = -1
                    }
                    else {
                        timeId = (nsMObject.value(forKey: "time_id") as! Int) - 1
                    }
                }
            }
        } catch {
            print(error.localizedDescription)
        }
        return timeId
    }
    
    func isTimeIdExist(timeId: Int) -> Bool {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
        fetchRequest.predicate = NSPredicate(format: "time_id = %d", timeId)
        do {
            let result = try nsManagedContext.fetch(fetchRequest)
            if result.count > 0 {
                return true
            }
            else {
                return false
            }
        } catch {
            print("Failed")
            return false
        }
    }
    
    /// Returns true if current or future time exist in the core data.
    func isCurrentOrFutureTimeExist(taskId: Int) -> Bool {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
        fetchRequest.predicate = NSPredicate(format: "task_id = %d && date = %@", taskId
            , getCurrentDate())
        do {
            let results = try nsManagedContext.fetch(fetchRequest)
            for result in results {
                let end = (result as! NSManagedObject).value(forKey: "end_time") as! Int
                let currentTime = getTimeInSec()
                if currentTime <= end {
                    return true
                }
            }
            return false
        } catch {
            print("Failed")
            return false
        }
    }
    
    /// get todays tasks time.
    func getTasksTime() -> Dictionary<Int, [Int]> {
        var dictArrValues: [Int: [Int]] = [:]
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
        fetchRequest.predicate = NSPredicate(format: "date == %@", getDate())
        let res = try! nsManagedContext.fetch(fetchRequest)
        for i in 0..<res.count {
            do {
                let nsMContext = try nsManagedContext.fetch(fetchRequest)
                let nsMObject = nsMContext[i] as! NSManagedObject
                let id = nsMObject.value(forKey: "task_id") as! Int
                let start = nsMObject.value(forKey: "start_time") as! Int
                let end = nsMObject.value(forKey: "end_time") as! Int
                let arrVal = [start, end]
                if var array = dictArrValues[id] {
                    array += arrVal
                    dictArrValues.updateValue(array, forKey: id)
                }
                else {
                    dictArrValues.updateValue(arrVal, forKey: id)
                }
                saveContext()
            }
            catch {
                print("Error")
            }
        }
        return dictArrValues
    }
    
    /// To get task total time from date abd task id.
    func getTaskTotalTime(nDate: Int64, taskId: Int) -> Int {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
        let strDate = Date().getStrDate(from: nDate)
        fetchRequest.predicate = NSPredicate(format: "date = %@ AND task_id = %d", strDate,
                                             taskId)
        let res = try! nsManagedContext.fetch(fetchRequest)
        var totalTime: Int = 0
        for i in 0..<res.count {
            do {
                let nsMContext = try nsManagedContext.fetch(fetchRequest)
                let nsMObject = nsMContext[i] as! NSManagedObject
                let start = nsMObject.value(forKey: "start_time") as! Int
                let end = nsMObject.value(forKey: "end_time") as! Int
                let nTime = end - start // Total time from start and end timings.
                totalTime += nTime
            }
            catch {
                print("Error")
            }
        }
        return totalTime
    }
    
    /// Get task time from date.
    func getTaskTotalTime(nDate: Int64) -> Int {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
        let strDate = Date().getStrDate(from: nDate)
        fetchRequest.predicate = NSPredicate(format: "date = %@", strDate)
        let res = try! nsManagedContext.fetch(fetchRequest)
        var totalTime: Int = 0
        for i in 0..<res.count {
            do {
                let nsMContext = try nsManagedContext.fetch(fetchRequest)
                let nsMObject = nsMContext[i] as! NSManagedObject
                let start = nsMObject.value(forKey: "start_time") as! Int
                let end = nsMObject.value(forKey: "end_time") as! Int
                let nTime = end - start
                totalTime += nTime
            }
            catch {
                print("Error")
            }
        }
        return totalTime
    }
    
    /// Get task time from task id.
    func getTaskTotalTime(taskId: Int) -> Int {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
        fetchRequest.predicate = NSPredicate(format: "task_id = %d", taskId)
        let res = try! nsManagedContext.fetch(fetchRequest)
        var totalTime: Int = 0
        for i in 0..<res.count {
            do {
                let nsMContext = try nsManagedContext.fetch(fetchRequest)
                let nsMObject = nsMContext[i] as! NSManagedObject
                let start = nsMObject.value(forKey: "start_time") as! Int
                let end = nsMObject.value(forKey: "end_time") as! Int
                let nTime = end - start
                totalTime += nTime
                
                // Check for start greater than end.
                if start > end {
                    // Get date difference from today's date.
                    let strDate = nsMObject.value(forKey: "date") as! String
                    let date = Date(strDateTime: "\(strDate) \(getSecondsToHoursMinutesSeconds(seconds: start))")
                    
                    // Take difference of two dates.
                    let nTimeStart = date.millisecondsSince1970
                    let nToday = Date().millisecondsSince1970
                    totalTime = Int(nToday - nTimeStart)
                }
            }
            catch {
                print("Error")
            }
        }
        return totalTime
    }
    
    /// Check previous day's task pending.(Returns array of task id.)
    func getPrevDaysPendingTasks() -> Array<TaskTimeDetails> {
        var arrTaskId = Array<TaskTimeDetails>()
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
        let dateToday = getDate()
        fetchRequest.predicate = NSPredicate(format: "end_time == %d AND date != %@", 0, dateToday)
        let res = try! nsManagedContext.fetch(fetchRequest)
        for i in 0..<res.count {
            let nsMContext = try! nsManagedContext.fetch(fetchRequest)
            let nsMObject = nsMContext[i] as! NSManagedObject
            let timeId = nsMObject.value(forKey: "time_id") as! Int
            let taskId = nsMObject.value(forKey: "task_id") as! Int
            let date = nsMObject.value(forKey: "date") as! String
            let start = nsMObject.value(forKey: "start_time") as! Int
            
            // Note time id is updated with task id.(Only here.)
            let timeDetails = TaskTimeDetails(timeId: timeId, taskId: taskId, date: date, start:
                start, end: 0, descr: "")
            arrTaskId.append(timeDetails)
        }
        return arrTaskId
    }
        
    /// Get task times from task id. Returns array of array, i.e, [[time id,Date, Start Time, End Time],...]
    func getTaskTimes(taskId: Int) -> Array<Array<Any>> {
        var arrTaskTimes = Array<Array<Any>>()
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
        fetchRequest.predicate = NSPredicate(format: "task_id = %d", taskId)
        let res = try! nsManagedContext.fetch(fetchRequest)
        for i in 0..<res.count {
            do {
                let nsMContext = try nsManagedContext.fetch(fetchRequest)
                let nsMObject = nsMContext[i] as! NSManagedObject
                let timeId = nsMObject.value(forKey: "time_id") as! Int
                let strDate = nsMObject.value(forKey: "date") as! String
                let start = nsMObject.value(forKey: "start_time") as! Int
                let end = nsMObject.value(forKey: "end_time") as! Int
                var descrptn: String!
                if let descr = nsMObject.value(forKey: "task_description") as? String {
                    descrptn = descr
                }
                else {
                    descrptn = ""
                }
                arrTaskTimes.append([timeId,strDate,start,end,descrptn!])
            }
            catch {
                print("Error")
            }
        }
        return arrTaskTimes
    }
    
    /// Get task times from task id. Returns array of array, i.e, [[time id,Date, Start Time, End Time],...]
    func getTaskTimes(taskId: Int) -> Array<TaskTimeDetails> {
        var arrTaskTimes = Array<TaskTimeDetails>()
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
        fetchRequest.predicate = NSPredicate(format: "task_id = %d", taskId)
        let res = try! nsManagedContext.fetch(fetchRequest)
        for i in 0..<res.count {
            do {
                let nsMContext = try nsManagedContext.fetch(fetchRequest)
                let nsMObject = nsMContext[i] as! NSManagedObject
                let timeId = nsMObject.value(forKey: "time_id") as! Int
                let strDate = nsMObject.value(forKey: "date") as! String
                let start = nsMObject.value(forKey: "start_time") as! Int
                let end = nsMObject.value(forKey: "end_time") as! Int
                let descr = nsMObject.value(forKey: "task_description") as? String
                
                let cTaskTimeDetails = TaskTimeDetails(timeId: timeId, taskId: taskId, date:
                    strDate, start: start, end: end, descr: descr)
                arrTaskTimes.append(cTaskTimeDetails)
            }
            catch {
                print("Error")
            }
        }
        return arrTaskTimes
    }
    
    func getTaskTimes(strDate: String, arrProj: Array<Int> = []) -> Array<TaskTimeDetails> {
        var arrTaskTimes = Array<TaskTimeDetails>()
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
        fetchRequest.predicate = NSPredicate(format: "date = %@", strDate)
        let res = try! nsManagedContext.fetch(fetchRequest)
        for i in 0..<res.count {
            do {
                let nsMContext = try nsManagedContext.fetch(fetchRequest)
                let nsMObject = nsMContext[i] as! NSManagedObject
                let timeId = nsMObject.value(forKey: "time_id") as! Int
                let taskId = nsMObject.value(forKey: "task_id") as! Int
                
                // Check projects.
                if arrProj.count > 0 {
                    // If project id not containing in the array.
                    let projId = getProjectId(taskId: taskId)
                    if !arrProj.contains(projId) {
                        continue
                    }
                }
                
                let strDate = nsMObject.value(forKey: "date") as! String
                let start = nsMObject.value(forKey: "start_time") as! Int
                let end = nsMObject.value(forKey: "end_time") as! Int
                let decr = nsMObject.value(forKey: "task_description") as? String
                
                let cTaskTimeDetails = TaskTimeDetails(timeId: timeId, taskId: taskId, date:
                    strDate, start: start, end: end, descr: decr)
                arrTaskTimes.append(cTaskTimeDetails)
            }
            catch {
                print("Error")
            }
        }
        return arrTaskTimes
    }
    
    
    /// get task id from date.
    func getTasksId(intDate: Int64) -> Array<Int> {
        var arrId: Array<Int> = []
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
        let strDate = Date().getStrDate(from: intDate)
        fetchRequest.predicate = NSPredicate(format: "date = %@", strDate)
        let res = try! nsManagedContext.fetch(fetchRequest)
        
        for i in 0..<res.count {
            do {
                let nsMContext = try nsManagedContext.fetch(fetchRequest)
                let nsMObject = nsMContext[i] as! NSManagedObject
                let id = nsMObject.value(forKey: "task_id") as! Int
                if !arrId.contains(id) {
                    arrId.append(id)
                }
            }
            catch {
                print("Error")
            }
        }
        return arrId
    }
    
    /// get task id from array if date.
    func getTasksId(arrIntDate: Array<Int64>) -> Array<Int> {
        var arrId: Array<Int> = []
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
        for intDate in arrIntDate {
            let strDate = Date().getStrDate(from: intDate)
            fetchRequest.predicate = NSPredicate(format: "date = %@", strDate)
            let res = try! nsManagedContext.fetch(fetchRequest)
            for i in 0..<res.count {
                do {
                    let nsMContext = try nsManagedContext.fetch(fetchRequest)
                    let nsMObject = nsMContext[i] as! NSManagedObject
                    let id = nsMObject.value(forKey: "task_id") as! Int
                    if !arrId.contains(id) {
                        arrId.append(id)
                    }
                }
                catch {
                    print("Error")
                }
            }
        }
        return arrId
    }
    
    /// get task time from date.
    func getTasksTime(intDate: Int64) -> Dictionary<Int, [Int]> {
        var dictArrValues: [Int: [Int]] = [:]
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
        let strDate = Date().getStrDate(from: intDate)
        fetchRequest.predicate = NSPredicate(format: "date == %@", strDate)
        let res = try! nsManagedContext.fetch(fetchRequest)
        
        for i in 0..<res.count {
            do {
                let nsMContext = try nsManagedContext.fetch(fetchRequest)
                let nsMObject = nsMContext[i] as! NSManagedObject
                let id = nsMObject.value(forKey: "task_id") as! Int
                let start = nsMObject.value(forKey: "start_time") as! Int
                let end = nsMObject.value(forKey: "end_time") as! Int
                let arrVal = [start, end]
                if var array = dictArrValues[id] {
                    array += arrVal
                    dictArrValues.updateValue(array, forKey: id)
                }
                else {
                    dictArrValues.updateValue(arrVal, forKey: id)
                }
            }
            catch {
                print("Error")
            }
        }
        return dictArrValues
    }
    
    /// update timings.
    func updateTime(taskId: Int, isSynched: Bool) {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
        fetchRequest.predicate = NSPredicate(format: "task_id = %d", taskId)
        // If synched with server.
        if isSynched {
            fetchRequest.sortDescriptors = [NSSortDescriptor(key: "time_id", ascending: false)]
        }
        else {
            fetchRequest.sortDescriptors = [NSSortDescriptor(key: "time_id", ascending: true)]
        }
        let res = try! nsManagedContext.fetch(fetchRequest)
        if res.count > 0 {
            let nsMObject = res[0] as! NSManagedObject
            nsMObject.setValue(getTimeInSec(), forKey: "end_time")
            saveContext()
        }
    }
    
    /// To get all available task time dates excluding duplicate.
    func getAllDates() -> Array<Int64> {
        var arrDates = Array<Int64>()
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
        do {
            let results = try nsManagedContext.fetch(fetchRequest)
            for result in results {
                let strDate = (result as! NSManagedObject).value(forKey: "date") as! String
                let date = getDateFromString(strDate: strDate)
                let nDate = date.millisecondsSince1970
                if !arrDates.contains(nDate) {
                    arrDates.append(nDate)
                }
            }
        }
        catch {
            print("Error")
        }
        arrDates.sort(by: >)
        return arrDates
    }
    
    /// To fetch all the dta from core data.
    func fetchAllData() {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
        do {
            let result = try nsManagedContext.fetch(fetchRequest)
            var array : Array<Array<Any>> = []
            for data in result as! [NSManagedObject] {
                var arr: Array<Any> = []
                arr.append(data.value(forKey: "time_id") as! Int)
                arr.append(data.value(forKey: "end_time") as! Int)
                arr.append(data.value(forKey: "start_time") as! Int)
                arr.append(data.value(forKey: "task_id") as! Int)
                arr.append(data.value(forKey: "date") as! String)
                array.append(arr)
            }
            print(array)
        } catch {
            print("Failed")
        }
    }
    
    // To update timings details.
    func updateDateTimings(timeId: Int, date: String, startTime: Int, endTime: Int, descr: String) {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
        fetchRequest.predicate = NSPredicate(format: "time_id = %d", timeId)
        let res = try! nsManagedContext.fetch(fetchRequest)
        if res.count > 0 {
            let nsMObject = res[0] as! NSManagedObject
            nsMObject.setValue(date, forKey: "date")
            nsMObject.setValue(startTime, forKey: "start_time")
            if endTime == 0 {
                nsMObject.setValue(0, forKey: "end_time")
            }
            else {
                nsMObject.setValue(endTime, forKey: "end_time")
            }
            nsMObject.setValue(descr, forKey: "task_description")
            saveContext()
        }
    }
    
    /// Delete task timings from time id.
    func deleteTaskTime(timeId: Int) {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
        fetchRequest.predicate = NSPredicate(format: "time_id = %d", timeId)
        let res = try! nsManagedContext.fetch(fetchRequest)
        if res.count > 0 {
            let nsMObject = res[0] as! NSManagedObject
            // delete selected row.
            nsManagedContext.delete(nsMObject)
            saveContext()
        }
    }
    
    /// Delete task timings from time id.
    func deletedOfflineTimes(taskId: Int) {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
        fetchRequest.predicate = NSPredicate(format: "task_id = %d", taskId)
        let res = try! nsManagedContext.fetch(fetchRequest)
        for nsMObject in res {
            let nsMObject = nsMObject as! NSManagedObject
            // Delete if offline time id.
            if (nsMObject.value(forKey: "time_id") as! Int) < 0 {
                // delete selected row.
                nsManagedContext.delete(nsMObject)
                saveContext()
            }
        }
    }

    /// To get available timings from task id.
    func getTimingsCount(of taskId: Int) -> Int {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
        fetchRequest.predicate = NSPredicate(format: "task_id = %d", taskId)
        let res = try! nsManagedContext.fetch(fetchRequest)
        return res.count
    }
    
    /// To get all time ids from task id.
    func getTimingsId(of taskId: Int) -> Array<Int> {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
        fetchRequest.predicate = NSPredicate(format: "task_id = %d", taskId)
        let results = try! nsManagedContext.fetch(fetchRequest)
        var arrTimeId: Array<Int> = []
        for result in results {
            let timeId = (result as! NSManagedObject).value(forKey: "time_id") as! Int
            arrTimeId.append(timeId)
        }
        return arrTimeId
    }
    
    /// Returns day timings ratio based on task timings.
    func getTaskRatioBasedOnProject(intDate: Int64, arrProj: Array<Int>?) -> Dictionary<Int, CGFloat> {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
        let strDate = Date().getStrDate(from: intDate)
        fetchRequest.predicate = NSPredicate(format: "date = %@", strDate)
        var dictRatio: Dictionary<Int, CGFloat> =  Dictionary<Int, CGFloat>()
        var totalTime:CGFloat = 0
        do {
            let res = try nsManagedContext.fetch(fetchRequest)
            for i in 0..<res.count {
                let object = res[i] as! NSManagedObject
                let taskId = object.value(forKey: "task_id") as! Int
                let projId = getProjectId(taskId: taskId)
                
                // Check project filter applied.
                if nil != arrProj {
                    if !(arrProj?.contains(projId))! {
                        continue
                    }
                }
                
                let workStart = object.value(forKey: "start_time") as! Int
                let workEnd = object.value(forKey: "end_time") as! Int
                let total = workEnd - workStart
                // get project id of task
                
                // Store each projects total work time.
                if nil == dictRatio[projId] {
                    dictRatio[projId] = CGFloat(total)
                }
                else {
                    dictRatio[projId]! += CGFloat(total)
                }
                totalTime += CGFloat(total)
            }
        } catch {
            print("Eror")
        }
        
        // Get each projects ratio.
        for (key, value) in dictRatio {
            dictRatio[key] = value/totalTime
        }
        return dictRatio
    }
    
    /// Get total work time from date.
    func getTotalWorkTime(intDate: Int64, arrProj: Array<Int>? = []) -> Int {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
        let strDate = Date().getStrDate(from: intDate)
        fetchRequest.predicate = NSPredicate(format: "date = %@", strDate)
        fetchRequest.sortDescriptors = [NSSortDescriptor(key: "start_time", ascending: true)]
        var arrAllTimes: Array<Array<Int>> = []
        do {
            let res = try nsManagedContext.fetch(fetchRequest)
            for i in 0..<res.count {
                let object = res[i] as! NSManagedObject
                
                // If project filter applied.
                if nil != arrProj && arrProj!.count > 0 {
                    let taskId = object.value(forKey: "task_id") as! Int
                    let projId = getProjectId(taskId: taskId)
                    if !arrProj!.contains(projId) {
                        continue
                    }
                }
                
                let workStart = object.value(forKey: "start_time") as! Int
                let workEnd = object.value(forKey: "end_time") as! Int
                arrAllTimes.append([workStart, workEnd])
            }
        } catch {
            print("Eror")
        }
        // Sort array timings based on start time.
//        let sortedArray = arrAllTimes.sorted(by: {
//            $0[0] < $1[0]
//        })
//        arrAllTimes = sortedArray // Assign sorted array to all timings.
        
        // array to store only reuired timings.(Removed duplicate tim entry)
        var arrReqTimes: Array<Array<Int>> = arrAllTimes
        
        for i in 0..<arrAllTimes.count {
            // Get start and end time.
            let start = arrAllTimes[i][0]
            let end = arrAllTimes[i][1]
            for j in 0..<arrAllTimes.count {
                // Dont compare with same start and end times.
                if i != j {
                    let anyOtherStart = arrAllTimes[j][0]
                    let anyOtherEnd = arrAllTimes[j][1]
                    // If any other timings in between start and end time.
                    if anyOtherStart > start && anyOtherEnd < end {
                        // If that timings exist in the required timings array.
                        // Remove that element to avoid dublicate timings.
                        if let index = arrReqTimes.firstIndex(of: [anyOtherStart, anyOtherEnd]) {
                            arrReqTimes.remove(at: index)
                        }
                    }
                }
            }
        }
        
        // Required array elements.
        var nTotalTime = 0
        if arrReqTimes.count > 0 {
            // Initial value load
            nTotalTime = arrReqTimes[0][1] - arrReqTimes[0][0]
            for i in 0..<arrReqTimes.count-1 {
                // i th index, end time
                let endTime1 = arrReqTimes[i][1]
                
                // i+1 th index, start and end time.
                let startTime2 = arrReqTimes[i+1][0]
                let endTime2 = arrReqTimes[i+1][1]
                
                // If i th end time greater than i+1 th start time
                if endTime1 >= startTime2 {
                    // Take total from end[i+1] - end[i]
                    nTotalTime += endTime2 - endTime1
                }
                else {
                    nTotalTime += endTime2 - startTime2
                }
            }
        }
        return nTotalTime
    }
    
    /// Delets all data from Tasks_time entity.
    func deleteAllData() {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks_time")
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
}
