//
//  TaskUpdater.swift
//  Attendance Manager
//
//  Created by Sachin on 9/19/19.
//  Copyright © 2019 Sachin. All rights reserved.
//

import Foundation
import UIKit
import CoreData

class TaskUpdater {
    var nsMOForUserTimes: NSManagedObject!
    var nsManagedContext: NSManagedObjectContext!
    var projectUpdater: AddProjects!
    
    init() {
        guard let appDelegate = UIApplication.shared.delegate as? AppDelegate else { return }
        nsManagedContext = appDelegate.persistentContainer.viewContext
        projectUpdater = AddProjects()
    }
    
    func addNewTask(projectId: Int, taskName: String, taskDesc: String) {
        //Creates a new task with name, description and project id.
        let userEntity = NSEntityDescription.entity(forEntityName: "Tasks", in: nsManagedContext)!
        nsMOForUserTimes = NSManagedObject(entity: userEntity, insertInto: nsManagedContext)
        var count = 0
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        do {
            count = try nsManagedContext.count(for: fetchRequest)
        } catch {
            print(error.localizedDescription)
        }
        nsMOForUserTimes.setValuesForKeys(["taskId": count, "projectId": projectId, "taskName":
            taskName, "taskDescription": taskDesc, "bWorkInProcess": false])
        do {
            try nsManagedContext.save()
        } catch let error as NSError {
            print("Could not save. \(error), \(error.userInfo)")
        }
    }
    
    func saveContext() {
        do {
            try nsManagedContext.save()
        } catch let error as NSError {
            print("Could not save. \(error), \(error.userInfo)")
        }
    }
    
    func getTaskId(taskName: String) -> Int {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "taskName == %@", taskName)
        var taskId: Int!
        do {
            let test = try nsManagedContext.fetch(fetchRequest)
            let objectUpdate = test[0] as! NSManagedObject
            taskId = objectUpdate.value(forKey: "taskId") as? Int
        }
        catch {
            print("Error")
        }
        print("Task id:\(String(describing: taskId))")
        return taskId
    }
    
    func getPreviouslyAddedTaskId() -> Int {
        var idTask: Int!
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.sortDescriptors = [NSSortDescriptor(key: "taskId", ascending: false)]
        do {
            let results = try nsManagedContext.fetch(fetchRequest) as! [NSManagedObject]
            idTask = (results[0].value(forKey: "taskId") as! Int)
        }
        catch {
            print("Error")
        }
        return idTask
    }
    
    func userStartsBreak() {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "bWorkInProcess == %@", NSNumber(value: true))
        
        let res = try! nsManagedContext.fetch(fetchRequest)
        if res.count > 0 {
            //User starts break.
            do {
                let test = try nsManagedContext.fetch(fetchRequest)
                let objectUpdate = test[0] as! NSManagedObject
                let nPrevTime = objectUpdate.value(forKey: "workStartTime") as! Int
                if nPrevTime != 0 {
                    let prevTotalWork = objectUpdate.value(forKey: "totalTime") as! Int
                    let totalWork = prevTotalWork + getTimeInSec() - nPrevTime
                    objectUpdate.setValue(totalWork, forKey: "totalTime")
                    saveContext()
                }
                objectUpdate.setValue(0, forKey: "workStartTime")
                saveContext()
            }
            catch {
                print("Error")
            }
        }
    }
    
    func userFinishedBreak() {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "bWorkInProcess == %@", NSNumber(value: true))
        
        let res = try! nsManagedContext.fetch(fetchRequest)
        if res.count > 0 {
            //User finishes break.
            do {
                let test = try nsManagedContext.fetch(fetchRequest)
                let objectUpdate = test[0] as! NSManagedObject
                objectUpdate.setValue(true, forKey: "bWorkInProcess")
                objectUpdate.setValue(getTimeInSec(), forKey: "workStartTime")
                saveContext()
            }
            catch {
                print("Error")
            }
        }
    }
    
    func userFinishedWork() {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "bWorkInProcess == %@", NSNumber(value: true))
        
        let res = try! nsManagedContext.fetch(fetchRequest)
        if res.count > 0 {
            //User finishes work.
            do {
                let test = try nsManagedContext.fetch(fetchRequest)
                let objectUpdate = test[0] as! NSManagedObject
                let nPrevTime = objectUpdate.value(forKey: "workStartTime") as! Int
                if nPrevTime != 0 {
                    let prevTotalWork = objectUpdate.value(forKey: "totalTime") as! Int
                    let totalWork = prevTotalWork + getTimeInSec() - nPrevTime
                    objectUpdate.setValue(totalWork, forKey: "totalTime")
                    objectUpdate.setValue(getTimeInSec(), forKey: "workStartTime")
                    saveContext()
                }
            }
            catch {
                print("Error")
            }
        }
    }
    
    func startTask(taskId: Int) {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "bWorkInProcess == %@", NSNumber(value: true))
        
        let res = try! nsManagedContext.fetch(fetchRequest)
        if res.count > 0 {
            //Any task in process stop it.
            do {
                let test = try nsManagedContext.fetch(fetchRequest)
                let objectUpdate = test[0] as! NSManagedObject
                objectUpdate.setValue(false, forKey: "bWorkInProcess")
                let nPrevTime = objectUpdate.value(forKey: "workStartTime") as! Int
                if nPrevTime != 0 {
                    //If user in break.
                    let prevTotalWork = objectUpdate.value(forKey: "totalTime") as! Int
                    let totalWork = prevTotalWork + getTimeInSec() - nPrevTime
                    objectUpdate.setValue(totalWork, forKey: "totalTime")
                    saveContext()
                }
                objectUpdate.setValue(0, forKey: "workStartTime")
                saveContext()
            }
            catch {
                print("Error")
            }
        }
        fetchRequest.predicate = NSPredicate(format: "taskId == %d", taskId)
        do {
            //Update task timings information.
            let test = try nsManagedContext.fetch(fetchRequest)
            let objectUpdate = test[0] as! NSManagedObject
            objectUpdate.setValue(true, forKey: "bWorkInProcess")
            objectUpdate.setValue(getTimeInSec(), forKey: "workStartTime")
            if objectUpdate.value(forKey: "startTime") as! Int == 0 {
                //If first time task started.
                objectUpdate.setValue(getTimeInSec(), forKey: "startTime")
                objectUpdate.setValue(getDate(), forKey: "startDate")
                objectUpdate.setValue(getDate(), forKey: "workDate")
                saveContext()
            }
        }
        catch {
            print("Error")
        }
    }
    
    func updateTaskNameDescrAndProject(taskId: Int, strTaskName: String, strDescr: String,
                                projectId: Int) {
        //Update task details.
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "taskId == %d", taskId)
        do {
            let test = try nsManagedContext.fetch(fetchRequest)
            let objectUpdate = test[0] as! NSManagedObject
            
            objectUpdate.setValue(projectId, forKey: "projectId")
            objectUpdate.setValue(strTaskName, forKey: "taskName")
            objectUpdate.setValue(strDescr, forKey: "taskDescription")
            
            saveContext()
        }
        catch {
            print("Error")
        }
    }
    
    func updateUserTaskTime() {
        //Updates user work timings.
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "bWorkInProcess == %@", NSNumber(value: true))
        
        let res = try! nsManagedContext.fetch(fetchRequest)
        
        if res.count > 0 {
            do {
                let test = try nsManagedContext.fetch(fetchRequest)
                let objectUpdate = test[0] as! NSManagedObject
                
                let workDate = objectUpdate.value(forKey: "workDate") as! String
                if workDate != getDate() {
                    objectUpdate.setValue(getDate(), forKey: "workDate")
                    objectUpdate.setValue(getTimeInSec(), forKey: "workStartTime")
                    saveContext()
                }

                let nPrevTime = objectUpdate.value(forKey: "workStartTime") as! Int
                print("hehe\(nPrevTime)")
                if nPrevTime != 0 {
                    let prevTotalWork = objectUpdate.value(forKey: "totalTime") as! Int
                    let totalWork = prevTotalWork + getTimeInSec() - nPrevTime
                    objectUpdate.setValue(totalWork, forKey: "totalTime")
                    objectUpdate.setValue(getTimeInSec(), forKey: "workStartTime")
                    saveContext()
                }
            }
            catch {
                print("Error")
            }
        }
    }
    
    func getRowCount() -> Int {
        //returns row count.
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        var count: Int!
        do {
            let result = try nsManagedContext.fetch(fetchRequest)
            count = result.count
        } catch {
            print("Failed")
        }
        return count
    }
    
    func fetchAllData() {
        //Fetches all the data available in the Tasks entity.
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        do {
            let result = try nsManagedContext.fetch(fetchRequest)
            print(result.count)
            for data in result as! [NSManagedObject] {
                print("Task Id: \(data.value(forKey: "taskId") as! Int)")
                print("Project Id: \(data.value(forKey: "projectId") as! Int)")
                print("Task Name: \(data.value(forKey: "taskName") as! String)")
                print("Task Descr: \(data.value(forKey: "taskDescription") as! String)")
                print("Start Time: \(data.value(forKey: "startTime") as! Int)")
                print("End Time: \(data.value(forKey: "endTime") as! Int)")
                print("Total Time: \(data.value(forKey: "totalTime") as! Int)")
                print("Work Start Time: \(data.value(forKey: "workStartTime") as! Int)")
                print("Work in Process: \(data.value(forKey: "bWorkInProcess") as! Bool)")
                print("Start date: \(String(describing: data.value(forKey: "startDate") as? String))")
                print("End date: \(String(describing: data.value(forKey: "endDate") as? String))")
            }
        } catch {
            print("Failed")
        }
    }
    
    func getTaskDetailsFromProjectName(arrProj: Array<String>) -> Array<Dictionary<String, Any>> {
        var arrTasks = Array<Dictionary<String, Any>>()
        for index in 0..<arrProj.count {
            let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
            let projId = projectUpdater.getProjectId(project: arrProj[index])
            fetchRequest.predicate = NSPredicate(format: "projectId = %d", projId)
            var dictResult = Dictionary<String, Any>()
            do {
                let test = try nsManagedContext.fetch(fetchRequest)
                for task in 0..<test.count {
                    let objectUpdate = test[task] as! NSManagedObject
                    let taskId = objectUpdate.value(forKey: "taskId") as? Int
                    dictResult.updateValue(taskId!, forKey: "Task Id")
                    let projId = objectUpdate.value(forKey: "projectId") as? Int
                    dictResult.updateValue(projId!, forKey: "Project Id")
                    let taskName = objectUpdate.value(forKey: "taskName") as? String
                    dictResult.updateValue(taskName!, forKey: "Task Name")
                    let taskDesc = objectUpdate.value(forKey: "taskDescription") as? String
                    dictResult.updateValue(taskDesc!, forKey: "Task Descr")
                    let startTime = objectUpdate.value(forKey: "startTime") as? Int
                    dictResult.updateValue(startTime!, forKey: "Start Time")
                    let endTime = objectUpdate.value(forKey: "endTime") as? Int
                    dictResult.updateValue(endTime!, forKey: "End Time")
                    let totalTime = objectUpdate.value(forKey: "totalTime") as? Int
                    dictResult.updateValue(totalTime!, forKey: "Total Time")
                    let workStart = objectUpdate.value(forKey: "workStartTime") as? Int
                    dictResult.updateValue(workStart!, forKey: "Work Start")
                    let bWorking = objectUpdate.value(forKey: "bWorkInProcess") as? Bool
                    dictResult.updateValue(bWorking!, forKey: "Work Process")
                    if let startDate = objectUpdate.value(forKey: "startDate"){
                        dictResult.updateValue(startDate, forKey: "Start Date")
                    }
                    if let EndDate = objectUpdate.value(forKey: "startDate") {
                        dictResult.updateValue(EndDate, forKey: "End Date")
                    }
                    arrTasks.append(dictResult)
                    dictResult.removeAll()
                }
            }
            catch {
                print("Error")
            }
        }
        return arrTasks
    }
    

    func getAllDates() -> Array<String> {
        var arrDates = Array<String>()
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.sortDescriptors = [NSSortDescriptor(key: "taskId", ascending: false)]
        do {
            let results = try nsManagedContext.fetch(fetchRequest)
            for result in results {
                let strDate = (result as! NSManagedObject).value(forKey: "startDate") as? String
                if strDate != nil {
                    if !arrDates.contains(strDate!) {
                        arrDates.append(strDate!)
                    }
                }
            }
        }
        catch {
            print("Error")
        }
        return arrDates
    }
    
    func getDayWiseDetails() -> Array<Dictionary<String, Any>> {
        var arrTasks = Array<Dictionary<String, Any>>()
        let arrDates = getAllDates()
        print("gj\(arrDates)")
        for index in 0..<arrDates.count {
            let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
            fetchRequest.predicate = NSPredicate(format: "startDate = %@", arrDates[index])
            do {
                let test = try nsManagedContext.fetch(fetchRequest)
                var totalTimeWork = 0
                for task in 0..<test.count {
                    let objectUpdate = test[task] as! NSManagedObject
                    let totalTime = objectUpdate.value(forKey: "totalTime") as? Int
                    totalTimeWork += totalTime!
                }
                var dictResult = Dictionary<String, Any>()
                dictResult.updateValue(totalTimeWork, forKey: "Total Work")
                dictResult.updateValue(arrDates[index], forKey: "Date")
                dictResult.updateValue(test.count, forKey: "Task Count")
                arrTasks.append(dictResult)
            }
            catch {
                print("Error")
            }
        }
        return arrTasks
    }
    
    func getMonthWiseDetails() -> Array<Dictionary<String, Any>> {
        var arrTasks = Array<Dictionary<String, Any>>()
        let arrDates = getAllDates()
        var totalTimeWork = 0
        var totalTask = 0
        for index in 0..<arrDates.count {
            let strMonth = getMonthName(strDate: arrDates[index])
            let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
            fetchRequest.predicate = NSPredicate(format: "startDate = %@", arrDates[index])
            do {
                let test = try nsManagedContext.fetch(fetchRequest)
                for task in 0..<test.count {
                    let objectUpdate = test[task] as! NSManagedObject
                    let totalTime = objectUpdate.value(forKey: "totalTime") as? Int
                    totalTimeWork += totalTime!
                    totalTask += 1
                }
            }
            catch {
                print("Error")
            }
            var flag = true
            for i in 0..<arrTasks.count {
                var dictValues = arrTasks[i]
                if (dictValues["Month"] as! String) == strMonth {
                    let nTotTime = dictValues["Total Work"] as! Int
                    dictValues.updateValue(nTotTime + totalTimeWork, forKey: "Total Work")
                    let nTask = dictValues["Task Count"] as! Int
                    dictValues.updateValue(totalTask + nTask, forKey: "Task Count")
                    let nDays = dictValues["Days"] as! Int
                    dictValues.updateValue(nDays + 1, forKey: "Days")
                    var arrDate = dictValues["Date"] as! Array<String>
                    arrDate.append(arrDates[index])
                    dictValues.updateValue(arrDate, forKey: "Date")
                    arrTasks[i] = dictValues
                    flag = false
                }
            }

            if flag {
                var dictResult = Dictionary<String, Any>()
                dictResult.updateValue(totalTimeWork, forKey: "Total Work")
                dictResult.updateValue(totalTask, forKey: "Task Count")
                dictResult.updateValue(1, forKey: "Days")
                dictResult.updateValue(strMonth, forKey: "Month")
                dictResult.updateValue(arrDates[index], forKey: "Date")
                let strDate = arrDates[index]
                let words = strDate.split(separator: "/")
                let strYear = String(words[2])
                dictResult.updateValue(strYear, forKey: "Year")
                dictResult.updateValue([arrDates[index]], forKey: "Date")
                arrTasks.append(dictResult)
            }
            totalTimeWork = 0
            totalTask = 0
        }
        
        return arrTasks
    }
    
    func getWeekWiseDetails() -> Array<Dictionary<String, Any>> {
        var arrTasks = Array<Dictionary<String, Any>>()
        let arrDates = getAllDates()
        var totalTimeWork = 0
        var totalTask = 0
        
        for index in 0..<arrDates.count {
            let nweek = getWeekNumber(strDate: arrDates[index])
            let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
            fetchRequest.predicate = NSPredicate(format: "startDate = %@", arrDates[index])
            do {
                let test = try nsManagedContext.fetch(fetchRequest)
                for task in 0..<test.count {
                    let objectUpdate = test[task] as! NSManagedObject
                    let totalTime = objectUpdate.value(forKey: "totalTime") as? Int
                    totalTimeWork += totalTime!
                    totalTask += 1
                }
            }
            catch {
                print("Error")
            }
            var flag = true
            for i in 0..<arrTasks.count {
                var dictValues = arrTasks[i]
                if (dictValues["Week"] as! Int) == nweek {
                    let nTotTime = dictValues["Total Work"] as! Int
                    dictValues.updateValue(nTotTime + totalTimeWork, forKey: "Total Work")
                    let nTask = dictValues["Task Count"] as! Int
                    dictValues.updateValue(totalTask + nTask, forKey: "Task Count")
                    let nDays = dictValues["Days"] as! Int
                    dictValues.updateValue(nDays + 1, forKey: "Days")
                    var arrDate = dictValues["Date"] as! Array<String>
                    arrDate.append(arrDates[index])
                    dictValues.updateValue(arrDate, forKey: "Date")
                    arrTasks[i] = dictValues
                    flag = false
                }
            }

            if flag {
                var dictResult = Dictionary<String, Any>()
                dictResult.updateValue(totalTimeWork, forKey: "Total Work")
                dictResult.updateValue(totalTask, forKey: "Task Count")
                dictResult.updateValue(1, forKey: "Days")
                dictResult.updateValue([arrDates[index]], forKey: "Date")
                dictResult.updateValue(nweek, forKey: "Week")
                arrTasks.append(dictResult)
            }
            totalTimeWork = 0
            totalTask = 0
        }
        return arrTasks
    }

    func getTotalTaskCount(arrProj: Array<String>) -> Int {
        var count = 0
        for index in 0..<arrProj.count {
            let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
            let projId = projectUpdater.getProjectId(project: arrProj[index])
            fetchRequest.predicate = NSPredicate(format: "projectId = %d", projId)
            do {
                let test = try nsManagedContext.fetch(fetchRequest)
                count += test.count
            }
            catch {
                print("Error")
            }
        }
        return count
    }
    
    func deleteAllData() {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
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
    
    func getDataFromDate(arrDate: Array<String>) -> Array<[String: Any]> {
        var arrDetails = Array<Dictionary<String, Any>>()
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        for date in arrDate {
            fetchRequest.predicate = NSPredicate(format: "startDate = %@", date)
            var dictResult = Dictionary<String, Any>()
            do {
                let test = try nsManagedContext.fetch(fetchRequest)
                for result in test {
                    let objectUpdate = result as! NSManagedObject
                    let projId = objectUpdate.value(forKey: "projectId") as? Int
                    dictResult.updateValue(projId!, forKey: "Project Id")
                    let taskName = objectUpdate.value(forKey: "taskName") as? String
                    dictResult.updateValue(taskName!, forKey: "Task Name")
                    let taskDesc = objectUpdate.value(forKey: "taskDescription") as? String
                    dictResult.updateValue(taskDesc!, forKey: "Task Descr")
                    let startTime = objectUpdate.value(forKey: "startTime") as? Int
                    dictResult.updateValue(startTime!, forKey: "Start Time")
                    let endTime = objectUpdate.value(forKey: "endTime") as? Int
                    dictResult.updateValue(endTime!, forKey: "End Time")
                    let totalTime = objectUpdate.value(forKey: "totalTime") as? Int
                    dictResult.updateValue(totalTime!, forKey: "Total Time")
                    let workStart = objectUpdate.value(forKey: "workStartTime") as? Int
                    dictResult.updateValue(workStart!, forKey: "Work Start")
                    let bWorking = objectUpdate.value(forKey: "bWorkInProcess") as? Bool
                    dictResult.updateValue(bWorking!, forKey: "Work Process")
                    if let startDate = objectUpdate.value(forKey: "startDate"){
                        dictResult.updateValue(startDate, forKey: "Start Date")
                    }
                    if let EndDate = objectUpdate.value(forKey: "startDate") {
                        dictResult.updateValue(EndDate, forKey: "End Date")
                    }
                    arrDetails.append(dictResult)
                }
            }
            catch {
                print("Error")
            }
        }
        return arrDetails
    }

    func getAllData(taskId: Int) -> [String: Any] {
        //Returns dictionary of data in a row.
        print(taskId)
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "taskId = %d", taskId)
        fetchRequest.sortDescriptors = [NSSortDescriptor(key: "taskId", ascending: false)]
        var dictResult = Dictionary<String, Any>()
        do {
            let test = try nsManagedContext.fetch(fetchRequest)
            let objectUpdate = test[0] as! NSManagedObject
            
            let projId = objectUpdate.value(forKey: "projectId") as? Int
            dictResult.updateValue(projId!, forKey: "Project Id")
            let taskName = objectUpdate.value(forKey: "taskName") as? String
            dictResult.updateValue(taskName!, forKey: "Task Name")
            let taskDesc = objectUpdate.value(forKey: "taskDescription") as? String
            dictResult.updateValue(taskDesc!, forKey: "Task Descr")
            let startTime = objectUpdate.value(forKey: "startTime") as? Int
            dictResult.updateValue(startTime!, forKey: "Start Time")
            let endTime = objectUpdate.value(forKey: "endTime") as? Int
            dictResult.updateValue(endTime!, forKey: "End Time")
            let totalTime = objectUpdate.value(forKey: "totalTime") as? Int
            dictResult.updateValue(totalTime!, forKey: "Total Time")
            let workStart = objectUpdate.value(forKey: "workStartTime") as? Int
            dictResult.updateValue(workStart!, forKey: "Work Start")
            let bWorking = objectUpdate.value(forKey: "bWorkInProcess") as? Bool
            dictResult.updateValue(bWorking!, forKey: "Work Process")
            if let startDate = objectUpdate.value(forKey: "startDate"){
                dictResult.updateValue(startDate, forKey: "Start Date")
            }
            if let EndDate = objectUpdate.value(forKey: "startDate") {
                dictResult.updateValue(EndDate, forKey: "End Date")
            }
        }
        catch {
            print("Error")
        }
        return dictResult
    }
}
