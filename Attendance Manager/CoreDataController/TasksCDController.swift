 /*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : ProjectsCDController.swift
 //
 //    File Created      : 19:Sept:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : Tasks entity Core data hndler.
 //
 //////////////////////////////////////////////////////////////////////////// */

import Foundation
import UIKit
import CoreData

class TasksCDController {
    var nsMOForUserTimes: NSManagedObject!
    var nsManagedContext: NSManagedObjectContext!
    var projectCDCtrlr: ProjectsCDController!
    var tasksTimeCDCtrlr: TasksTimeCDController!
    
    init() {
        guard let appDelegate = UIApplication.shared.delegate as? AppDelegate else { return }
        nsManagedContext = appDelegate.persistentContainer.viewContext
        projectCDCtrlr = ProjectsCDController()
        tasksTimeCDCtrlr = TasksTimeCDController()
    }
    
    /// Creates a new task with name, description and project id.
    func addNewTask(projectId: Int, taskName: String, taskDesc: String, moduleId: Int, isSynched:
        Bool, isRunning: Bool, startTime: Int64 = 0) -> Int {
        let userEntity = NSEntityDescription.entity(forEntityName: "Tasks", in: nsManagedContext)!
        nsMOForUserTimes = NSManagedObject(entity: userEntity, insertInto: nsManagedContext)
        var taskId: Int!
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.sortDescriptors = [NSSortDescriptor(key: "task_id", ascending: true)]
        let results = try! nsManagedContext.fetch(fetchRequest)
        if results.count > 0 {
            let firstTaskId = (results.first as! NSManagedObject).value(forKey: "task_id")
                as! Int
            
            /// Offline task ids are stored in negative task id.
            if firstTaskId < 0 {
                taskId = firstTaskId - 1
            }
            else {
                taskId = -1
            }
        }
        else {
            taskId = -1
        }
        nsMOForUserTimes.setValuesForKeys(["task_id": taskId!, "project_id": projectId
            , "module_id": moduleId, "task_name": taskName, "task_description": taskDesc
            , "is_work_in_progress": isRunning,"is_synched": isSynched, "start_time": startTime])
        saveContext()
        return taskId
    }
    
    /// Check task is present.
    func isTaskExist(taskId: Int) -> Bool {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "task_id == %d", taskId)
        let res = try! nsManagedContext.fetch(fetchRequest)
        return res.count > 0 ? true : false
    }
    
    /// Add task details.
    func addOrUpdateTaskDetails(taskId: Int, taskName: String, taskDesc: String, projId: Int
        , moduleId: Int, bIsWorking: Bool, startTime: Int64, endTime: Int64?) {
        // If task id not exists add details.
        if !isTaskExist(taskId: taskId) {
            let userEntity = NSEntityDescription.entity(forEntityName: "Tasks", in:
                nsManagedContext)!
            nsMOForUserTimes = NSManagedObject(entity: userEntity, insertInto: nsManagedContext)
            if nil == endTime {
                nsMOForUserTimes.setValuesForKeys(["task_id": taskId, "project_id": projId,
                    "module_id": moduleId, "task_name": taskName, "task_description": taskDesc,
                    "is_work_in_progress": bIsWorking, "start_time": startTime, "is_synched": true])
            }
            else {
                nsMOForUserTimes.setValuesForKeys(["task_id": taskId, "project_id": projId,
                    "module_id": moduleId, "task_name": taskName, "task_description": taskDesc,
                        "is_work_in_progress": bIsWorking, "start_time": startTime,
                                                   "end_time": endTime!, "is_synched": true])
            }
        }
        else {
            updateTaskNameDescrAndProject(taskId: taskId, moduleId: moduleId, strTaskName: taskName
                , strDescr: taskDesc, projectId: projId, isWorking: bIsWorking, startTime: startTime
                , endTime: endTime, isSynched: nil, deleted: [])
        }
        saveContext()
    }
    
    /// If task is paused function will starts or stops. Returns true if task started.
    func updateOrSetStartTime(taskId: Int, startTime: Int64) {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "task_id == %d", taskId)
        do {
            //Update task start time.
            let nsMContext = try nsManagedContext.fetch(fetchRequest)
            let nsMObject = nsMContext[0] as! NSManagedObject
            nsMObject.setValue(startTime, forKey: "start_time")
        }
        catch {
            print("Error")
        }
        saveContext()
    }
    
    /// Commit database.
    func saveContext() {
        do {
            try nsManagedContext.save()
        } catch let error as NSError {
            print("Could not save. \(error), \(error.userInfo)")
        }
    }
    
    /// get project id from task id.
    func getProjectId(taskId: Int) -> Int {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "task_id == %d", taskId)
        var projId: Int!
        do {
            let nsMContext = try nsManagedContext.fetch(fetchRequest)
            let nsMObject = nsMContext[0] as! NSManagedObject
            projId = nsMObject.value(forKey: "project_id") as? Int
        }
        catch {
            print("Error")
        }
        return projId
    }
    
    /// Stops running tasks.
    func userFinishedWork() {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "is_work_in_progress == %@",
                                             NSNumber(value: true))
        let res = try! nsManagedContext.fetch(fetchRequest)
        if res.count > 0 {
            //User finishes work.
            do {
                let nsMContext = try nsManagedContext.fetch(fetchRequest)
                let nsMObject = nsMContext[0] as! NSManagedObject
                nsMObject.setValue(false, forKey: "is_work_in_process")
                saveContext()
            }
            catch {
                print("Error")
            }
        }
    }
    
    /// Updates task end time.
    func userFinishedTask(taskId: Int) {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "task_id = %d", taskId)
        let nsMContext = try! nsManagedContext.fetch(fetchRequest)
        if nsMContext.count > 0 {
            //User finishes work.
            let nsMObject = nsMContext[0] as! NSManagedObject
            nsMObject.setValue(Date().millisecondsSince1970, forKey: "end_time")
            nsMObject.setValue(false, forKey: "is_work_in_progress")
            saveContext()
        }
        
    }
    
    /// Start task from given task id.
    func startTask(taskId: Int, completion:@escaping (Bool) -> ()) {
        // If device not connected to internet, store it to coredata.
        if !RequestController.isConnectedToNetwork() {
            updateUserTaskTime()
            let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
            fetchRequest.predicate = NSPredicate(format: "task_id == %d", taskId)
            do {
                //Update task timings information.
                let nsMContext = try nsManagedContext.fetch(fetchRequest)
                let nsMObject = nsMContext[0] as! NSManagedObject
                nsMObject.setValue(true, forKey: "is_work_in_progress")
                nsMObject.setValue(false, forKey: "is_synched")
                if nsMObject.value(forKey: "start_time") as! Int64 == 0 {
                    nsMObject.setValue(Date().millisecondsSince1970, forKey: "start_time")
                }
                tasksTimeCDCtrlr.addStartTime(taskId: taskId)
                
                // For future refference store running task id.
                UserDefaults.standard.set(taskId, forKey: "previousTaskId")
                updateUserTaskTime()
                saveContext()
                completion(true)
            }
            catch {
                print("Error")
                completion(false)
            }
        }
        else {
            APIResponseHandler.startTaskOrPunchIn(taskId: String(taskId), completion: {
                status, msg  in
                if status {
                    completion(true)
                }
                else {
                    completion(false)
                }
            })
        }
    }
    
    /// Stop the given task.
    func stopTask(taskId: Int, completion:@escaping (Bool) -> ()) {
        // Id device not connected to internet.
        if !RequestController.isConnectedToNetwork() {
            let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
            fetchRequest.predicate = NSPredicate(format:
                "is_work_in_progress = %@ AND task_id = %d", NSNumber(value: true), taskId)
            do {
                // If any task running.
                //Update task timings information.
                let nsMContext = try nsManagedContext.fetch(fetchRequest)
                let nsMObject = nsMContext[0] as! NSManagedObject
                let taskId = nsMObject.value(forKey: "task_id") as! Int
                let isSynched = nsMObject.value(forKey: "is_synched") as! Bool
                tasksTimeCDCtrlr.updateTime(taskId: taskId, isSynched: isSynched)
                nsMObject.setValue(false, forKey: "is_work_in_progress")
                nsMObject.setValue(false, forKey: "is_synched")
                saveContext()
                completion(true)
            }
            catch {
                print("Error")
                completion(false)
            }
        }
        else {
            APIResponseHandler.stopTaskOrPunchIn(taskId: String(taskId), completion: {
                status in
                if status {
                    completion(true)
                }
                else {
                    completion(false)
                }
            })
        }
    }
    
    /// If task is paused function will starts or stops. Returns true if task started.
    func startOrStopTask(taskId: Int) -> Bool {
        updateUserTaskTime()
        
        var bProcessState: Bool!
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "task_id == %d", taskId)
        do {
            //Update task timings information.
            let nsMContext = try nsManagedContext.fetch(fetchRequest)
            let nsMObject = nsMContext[0] as! NSManagedObject
            
            if nsMObject.value(forKey: "is_work_in_progress") as! Bool == true {
                nsMObject.setValue(false, forKey: "is_work_in_progress")
                bProcessState = false
            }
            else {
                nsMObject.setValue(true, forKey: "is_work_in_progress")
                if nsMObject.value(forKey: "start_time") as! Int64 == 0 {
                    nsMObject.setValue(Date().millisecondsSince1970, forKey: "start_time")
                }
                tasksTimeCDCtrlr.addStartTime(taskId: taskId)
                // For future refference store running task id.
                UserDefaults.standard.set(taskId, forKey: "previousTaskId")
                bProcessState = true
                
                // Update time task time.
                updateUserTaskTime()
            }
        }
        catch {
            print("Error")
        }
        saveContext()
        return bProcessState
    }
    
    /// Any changes in task details updated to given task id.
    func updateTaskNameDescrAndProject(taskId: Int, moduleId: Int, strTaskName: String, strDescr:
        String, projectId: Int, isWorking: Bool, startTime: Int64 = 0, endTime: Int64? = 0,
                isSynched: Bool?, deleted: Array<String>) {
        var deletedRange = ""
        for str in deleted {
            deletedRange += "\(str),"
        }
        
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "task_id == %d", taskId)
        do {
            let nsMContext = try nsManagedContext.fetch(fetchRequest)
            // Delete timings.
            for timeId in deleted {
                let taskTimeCDCtrlr = TasksTimeCDController()
                taskTimeCDCtrlr.deleteTaskTime(timeId: Int(timeId)!)
            }
            let nsMObject = nsMContext[0] as! NSManagedObject
            nsMObject.setValue(projectId, forKey: "project_id")
            nsMObject.setValue(moduleId, forKey: "module_id")
            nsMObject.setValue(strTaskName, forKey: "task_name")
            nsMObject.setValue(strDescr, forKey: "task_description")
            nsMObject.setValue(isWorking, forKey: "is_work_in_progress")
            nsMObject.setValue(startTime, forKey: "start_time")
            nsMObject.setValue(deletedRange, forKey: "deleted_time_range")
            if nil == isSynched {
                let isSynched = nsMObject.value(forKey: "is_synched") as! Bool
                nsMObject.setValue(isSynched, forKey: "is_synched")
            }
            else {
                nsMObject.setValue(isSynched, forKey: "is_synched")
            }
            if let end = endTime {
                nsMObject.setValue(end, forKey: "end_time")
            }
            saveContext()
        }
        catch {
            print("Error")
        }
    }
    
    /// Get task times from task id. Returns array of array, i.e, [[Date, Start Time, End Time],...]
    func getTaskTimings(taskId: Int) -> Array<Array<Any>> {
        var arrTaskTimes = Array<Array<Any>>()
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "task_id = %d",taskId)
        let res = try! nsManagedContext.fetch(fetchRequest)
        let result = res[0] as! NSManagedObject
        let bTaskInProcess = result.value(forKey: "is_work_in_progress") as! Bool
        if res.count > 0 && !bTaskInProcess {
            arrTaskTimes = tasksTimeCDCtrlr.getTaskTimes(taskId: taskId)
        }
        return arrTaskTimes
    }
    
    /// Updates user work timings.
    func updateUserTaskTime() {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "is_work_in_progress == %@",
                                             NSNumber(value: true))
        let res = try! nsManagedContext.fetch(fetchRequest)
        for i in 0..<res.count {
            let nsMObject = res[i] as! NSManagedObject
            let taskId = nsMObject.value(forKey: "task_id") as! Int
            let isSynched = nsMObject.value(forKey: "is_synched") as! Bool
            tasksTimeCDCtrlr.updateTime(taskId: taskId, isSynched: isSynched)
            saveContext()
        }
    }
    
    /// Returns total task timings.
    func getTotalTime(taskId: Int) -> Int {
        var nTotTime = 0
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "task_id = %d",taskId)
        let res = try! nsManagedContext.fetch(fetchRequest)
        if res.count > 0 {
            nTotTime = tasksTimeCDCtrlr.getTaskRunningTime(taskId: taskId)
        }
        return nTotTime
    }
    
    /// To get details of task.
    func getDetails(taskId: Int) -> TaskDetails? {
        var nTotTime = 0
        var cTaskDetails: TaskDetails!
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "task_id = %d",taskId)
        let res = try! nsManagedContext.fetch(fetchRequest)
        if res.count > 0 {
            let nsMObject = res[0] as! NSManagedObject
            nTotTime = tasksTimeCDCtrlr.getTaskTotalTime(taskId: taskId)
            let taskName = nsMObject.value(forKey: "task_name") as! String
            let projId = nsMObject.value(forKey: "project_id") as! Int
            let startIntDateTime = nsMObject.value(forKey: "start_time") as! Int64
            let modId = nsMObject.value(forKey: "module_id") as! Int
             
            cTaskDetails = TaskDetails(taskId: taskId, taskName: taskName, taskDescr: nil, projId:
                projId, modId: modId, nTotalTime: nTotTime, nStartTime: startIntDateTime, nEndTime: nil,
                        isRunnung: nil)
        }
        return cTaskDetails
    }
    
    /// Returns task name.
    func getTaskName(taskId: Int) -> String {
        var strName = ""
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "task_id = %d",taskId)
        let res = try! nsManagedContext.fetch(fetchRequest)
        if res.count > 0 {
            let nsMObject = res[0] as! NSManagedObject
            strName = nsMObject.value(forKey: "task_name") as! String
        }
        return strName
    }
    
    func getTaskStartTime(taskId: Int) -> String {
        var strStartTime = ""
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "task_id = %d", taskId)
        let res = try! nsManagedContext.fetch(fetchRequest)
        if res.count > 0 {
            let nsMObject = res[0] as! NSManagedObject
            
            let startIntDateTime = nsMObject.value(forKey: "start_time") as! Int64
            
            let startDateTime = Date(milliseconds: startIntDateTime)
            
            let startTime = startDateTime.timeInDate
            let strTime = getSecondsToHoursMinutesSecondsWithAllFields(seconds: startTime)
            let str12HrTime = convert24to12Format(strTime: strTime)
            
            let strDate = Date().getStrDate(from: startIntDateTime)
            let strFormateDate = getDateDay(date: strDate)
            
            strStartTime = "Started \(strFormateDate) \(str12HrTime)"
        }
        return strStartTime
    }
    
    /// Stops currently running task.
    func stopRunningTask() {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "is_work_in_progress == %@",
                                             NSNumber(value: true))
        let results = try! nsManagedContext.fetch(fetchRequest)
        for res in results {
            let nsMObject = res as! NSManagedObject
            nsMObject.setValue(false, forKey: "is_work_in_progress")
            saveContext()
        }
    }
    
    ///
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
        var array : Array<Array<Any>> = [[]]
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        do {
            let result = try nsManagedContext.fetch(fetchRequest)
            for data in result as! [NSManagedObject] {
                var arr: Array<Any> = []
                arr.append(data.value(forKey: "task_id") as! Int)
                arr.append(data.value(forKey: "is_work_in_progress") as! Bool)
                arr.append(data.value(forKey: "project_id") as! Int)
                arr.append(data.value(forKey: "start_time") as! Int64)
                arr.append(data.value(forKey: "end_time") as! Int64)
                arr.append(data.value(forKey: "task_description") as! String)
                arr.append(data.value(forKey: "task_name") as! String)
                array.append(arr)
            }
        } catch {
            print("Failed")
        }
        print("Tasks : \(array)")
    }
    
    func isTaskRunning() -> Bool {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "is_work_in_progress = %d", 1)
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
    
    func getRunningTasks() -> Array<Int> {
        var arrTaskIds = Array<Int>()
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "is_work_in_progress = %d", 1)
        do {
            let nsMContext = try nsManagedContext.fetch(fetchRequest)
            for task in 0..<nsMContext.count {
                let nsMObject = nsMContext[task] as! NSManagedObject
                let taskId = nsMObject.value(forKey: "task_id") as! Int
                arrTaskIds.append(taskId)
            }
            return arrTaskIds
        }
        catch {
            print("Failed")
            return arrTaskIds
        }
    }
    
    func getTaskDetailsFromProjectName(arrProj: Array<String>) -> Array<Dictionary<String, Any>> {
        var arrTasks = Array<Dictionary<String, Any>>()
        for index in 0..<arrProj.count {
            let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
            let projId = projectCDCtrlr.getProjectId(project: arrProj[index])
            fetchRequest.predicate = NSPredicate(format: "project_id = %d", projId)
            var dictResult = Dictionary<String, Any>()
            do {
                let nsMContext = try nsManagedContext.fetch(fetchRequest)
                for task in 0..<nsMContext.count {
                    let nsMObject = nsMContext[task] as! NSManagedObject
                    let taskId = nsMObject.value(forKey: "task_id") as? Int
                    dictResult.updateValue(taskId!, forKey: "Task Id")
                    let projId = nsMObject.value(forKey: "project_id") as? Int
                    dictResult.updateValue(projId!, forKey: "Project Id")
                    let task_name = nsMObject.value(forKey: "task_name") as? String
                    dictResult.updateValue(task_name!, forKey: "Task Name")
                    let taskDesc = nsMObject.value(forKey: "task_description") as? String
                    dictResult.updateValue(taskDesc!, forKey: "Task Descr")
                    let startTime = nsMObject.value(forKey: "start_time") as! Int64
                    dictResult.updateValue(startTime, forKey: "Start Time")
                    
                    // get total work time from taskstime entity
                    let totalTime = tasksTimeCDCtrlr.getTaskTotalTime(nDate: startTime, taskId:
                        taskId!)
                    dictResult.updateValue(totalTime, forKey: "Total Time")
                    
                    let end_time = nsMObject.value(forKey: "end_time") as! Int64
                    dictResult.updateValue(end_time, forKey: "End Time")
                    let bWorking = nsMObject.value(forKey: "is_work_in_progress") as? Bool
                    dictResult.updateValue(bWorking!, forKey: "Work Process")
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
    
    /// Returns task details in array of dictionary .
    /// Requires project names.
    func getTaskDetailsFromProjectNameUnFinished(arrProj: Array<Int>, onlyTodays: Bool = false) ->
        Array<TaskDetails> {
            var arrCTaskDetails = Array<TaskDetails>()
        for projId in arrProj {
            let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
            fetchRequest.predicate = NSPredicate(format: "project_id = %d", projId)
            do {
                let nsMContext = try nsManagedContext.fetch(fetchRequest)
                for task in 0..<nsMContext.count {
                    var cTaskDetails: TaskDetails!
                    let nsMObject = nsMContext[task] as! NSManagedObject
                    if nsMObject.value(forKey: "end_time") as! Int64 == 0 {
                        // If End time not set, treated as unfinished task.
                        let taskId = nsMObject.value(forKey: "task_id") as! Int
                        let projId = nsMObject.value(forKey: "project_id") as! Int
                        let modId = nsMObject.value(forKey: "module_id") as! Int
                        let taskName = nsMObject.value(forKey: "task_name") as! String
                        let taskDesc = nsMObject.value(forKey: "task_description") as! String
                        let startDateTime = nsMObject.value(forKey: "start_time") as! Int64
                        let bWorking = nsMObject.value(forKey: "is_work_in_progress") as! Bool
                        let endTime = nsMObject.value(forKey: "end_time") as! Int64
                        
                        // Check for onlyTodays applies.
                        if onlyTodays {
                            let date = Date(milliseconds: startDateTime)
                            // Return only if todays timeloine exist, started today,
                            // currently running and not started.
                            if !tasksTimeCDCtrlr.isTodayTimeLineExist(taskId: taskId) &&
                                startDateTime != 0 && getCurrentDate() != date.getStrDate() &&
                                !bWorking {
                                // discard it.
                                continue
                            }
                        }
                        
                        // get total work time from taskstime entity
                        let totalTime = tasksTimeCDCtrlr.getTaskTotalTime(taskId: taskId)
                        
                        cTaskDetails = TaskDetails(taskId: taskId, taskName: taskName, taskDescr:
                            taskDesc, projId: projId, modId: modId, nTotalTime: totalTime, nStartTime:
                            startDateTime, nEndTime: endTime, isRunnung: bWorking)
                        arrCTaskDetails.append(cTaskDetails)
                    }}
            }
            catch {
                print("Error")
            }
            }
            arrCTaskDetails.sort { (task1, task2) -> Bool in
                return task1.taskId > task2.taskId
            }
            return arrCTaskDetails
    }
    
    /// To get all the dates.
    func getAllDates() -> Array<Int64> {
        var arrDates = Array<Int64>()
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.sortDescriptors = [NSSortDescriptor(key: "task_id", ascending: false)]
        do {
            let results = try nsManagedContext.fetch(fetchRequest)
            for result in results {
                let nDate = (result as! NSManagedObject).value(forKey: "start_time") as! Int64
                if !arrDates.contains(nDate) {
                    arrDates.append(nDate)
                }
            }
        }
        catch {
            print("Error")
        }
        return arrDates
    }
    
    /// To get date, task count and total work in every date.
    func getDayWiseDetails() -> Array<Dictionary<String, Any>> {
        var arrTasks = Array<Dictionary<String, Any>>()
        let arrDates = tasksTimeCDCtrlr.getAllDates()
        for index in 0..<arrDates.count {
            let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
            fetchRequest.sortDescriptors = [NSSortDescriptor(key: "task_id", ascending:
                false)]
            do {
                let nsMContext = try nsManagedContext.fetch(fetchRequest)
                var totalTimeWork = 0
                for task in 0..<nsMContext.count {
                    let nsMObject = nsMContext[task] as! NSManagedObject
                    
                    let taskId = nsMObject.value(forKey: "task_id") as! Int
                    let startTime = nsMObject.value(forKey: "start_time") as! Int64
                    
                    // get total work time from taskstime entity
                    let totalTime = tasksTimeCDCtrlr.getTaskTotalTime(nDate: startTime,
                                                                     taskId: taskId)
                    totalTimeWork += totalTime
                }
                var dictResult = Dictionary<String, Any>()
                dictResult.updateValue(totalTimeWork, forKey: "Total Work")
                dictResult.updateValue(arrDates[index], forKey: "Date")
                dictResult.updateValue(nsMContext.count, forKey: "Task Count")
                arrTasks.append(dictResult)
            }
            catch {
                print("Error")
            }
        }
        return arrTasks
    }
    
    /// To get date, task count and total work in every month.
    func getMonthWiseDetails(arrProj: Array<Int>?) -> Array<MonthDetails> {
        var arrMonthDetails = Array<MonthDetails>()
        let arrIntDates = tasksTimeCDCtrlr.getAllDates()
        var arrDates: Array<String> = []
        for intDate in arrIntDates {
            let strDate = Date().getStrDate(from: intDate)
            if !arrDates.contains(strDate){
                arrDates.append(strDate)
            }
        }
        
        for index in 0..<arrIntDates.count {
            var flag = true
            let strDate = arrDates[index]
            let strMonth = getMonthAndYear(strDate: strDate)
            for i in 0..<arrMonthDetails.count {
                let monthDetails = arrMonthDetails[i]
                // If already month index exist.
                if monthDetails.strMonthYear == strMonth {
                    monthDetails.addDate(nDate: arrIntDates[index])
                    flag = false
                }
            }
            if flag {
                // create new object. (If new week.)
                let monthDetails = MonthDetails(nDate: arrIntDates[index], arrProj: arrProj)
                arrMonthDetails.append(monthDetails)
            }
        }
        return arrMonthDetails
    }
    
    /// To get date, task count and total work in every week.
    func getWeekWiseDetails(arrProj: Array<Int>?) -> Array<WeekDetails> {
        var arrWeekDetails = Array<WeekDetails>()
        let arrIntDates = tasksTimeCDCtrlr.getAllDates()
        
        for index in 0..<arrIntDates.count {
            var flag = true
            let nWeek = getWeekNumber(nDate: arrIntDates[index])
            for i in 0..<arrWeekDetails.count {
                let weekDetails = arrWeekDetails[i]
                // If already weeknumber index exist.
                if weekDetails.weeknumber == nWeek {
                    weekDetails.addDate(nDate: arrIntDates[index])
                    flag = false
                }
            }
            if flag {
                // create new object. (If new week.)
                let weekDetails = WeekDetails(nDate: arrIntDates[index], arrProj: arrProj)
                arrWeekDetails.append(weekDetails)
            }
        }
        return arrWeekDetails
    }

    /// To get total tasks count.
    func getTotalTaskCount(arrProj: Array<String>) -> Int {
        var count = 0
        for index in 0..<arrProj.count {
            let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
            let projId = projectCDCtrlr.getProjectId(project: arrProj[index])
            fetchRequest.predicate = NSPredicate(format: "project_id = %d", projId)
            do {
                let nsMContext = try nsManagedContext.fetch(fetchRequest)
                count += nsMContext.count
            }
            catch {
                print("Error")
            }
        }
        return count
    }
    
    /// To get total task count which are not finished.
    func getTotalTaskCountUnFinished(arrProj: Array<Int>) -> Int {
        var count = 0
        for projId in arrProj {
            let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
            fetchRequest.predicate = NSPredicate(format: "project_id = %d", projId)
            do {
                let nsMContext = try nsManagedContext.fetch(fetchRequest)
                for result in nsMContext {
                    let nsMObject = result as! NSManagedObject
                    if nsMObject.value(forKey: "end_time") as! Int64 == 0 {
                        count += 1
                    }
                }
            }
            catch {
                print("Error")
            }
        }
        return count
    }
    
    /// Deletes all the data in Tasks entiy.
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
        tasksTimeCDCtrlr.deleteAllData()
    }
    
    /// Get all the tasks details from provided array of dates.
    func getDataFromDate(arrDate: Array<Int64>, arrProj: Array<Int>?) -> Array<TaskDetails> {
        let arrTaskId = tasksTimeCDCtrlr.getTasksId(arrIntDate: arrDate)
        let arrTaskDetails = getDataFromIds(taskIds: arrTaskId, arrProj: arrProj)
        return arrTaskDetails
    }
    
    /// To get all the task details from provide array of task ids.
    func getDataFromIds(taskIds: Array<Int>, arrProj: Array<Int>?) -> Array<TaskDetails> {
        var arrDetails = Array<TaskDetails>()
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.sortDescriptors = [NSSortDescriptor(key: "start_time", ascending:
            false)]
        for taskId in taskIds {
            // Apply filter.
            if nil != arrProj || arrProj?.count == 0 {
                fetchRequest.predicate = NSPredicate(format:
                    "task_id = %d AND project_id IN %@", taskId, arrProj!)
            }
            else {
                fetchRequest.predicate = NSPredicate(format:
                    "task_id = %d", taskId)
            }
            do {
                let nsMContext = try nsManagedContext.fetch(fetchRequest)
                for result in nsMContext {
                    let nsMObject = result as! NSManagedObject
                    let projId = nsMObject.value(forKey: "project_id") as! Int
                    let taskId = nsMObject.value(forKey: "task_id") as! Int
                    let modId = nsMObject.value(forKey: "module_id") as! Int
                    let taskName = nsMObject.value(forKey: "task_name") as! String
                    let taskDesc = nsMObject.value(forKey: "task_description") as! String
                    let totalTime = tasksTimeCDCtrlr.getTaskTotalTime(taskId: taskId)
                    let bWorking = nsMObject.value(forKey: "is_work_in_progress") as! Bool

                    let nStartDateTime = nsMObject.value(forKey: "start_time") as! Int64
                    let nEndDateTime = nsMObject.value(forKey: "end_time") as! Int64
                    
                    let cTaskDetails = TaskDetails(taskId: taskId, taskName: taskName, taskDescr:
                        taskDesc, projId: projId, modId: modId, nTotalTime: totalTime
                        , nStartTime: nStartDateTime, nEndTime: nEndDateTime, isRunnung: bWorking)
                    
                    arrDetails.append(cTaskDetails)
                }
            }
            catch {
                print("Error")
            }
        }
        
        // Sort array details based on date.
        arrDetails.sort { (task1, task2) -> Bool in
            return task1.nStartTime! > task2.nStartTime!
        }
        return arrDetails
    }
    
    func getEachTaskTimeDataFromDate(intDate: Int64, arrProj: Array<Int>? = nil)
        -> Array<TaskTimeDetails> {
            var arrDetails = Array<TaskTimeDetails>()
            let strDate = Date(milliseconds: intDate).getStrDate()
            arrDetails = tasksTimeCDCtrlr.getTaskTimes(strDate: strDate, arrProj: arrProj)
            
            // Sort based on end time.
            arrDetails.sort { (task1, task2) -> Bool in
                return task1.nEndTime > task2.nEndTime
            }
            
            // Sort based on start time.
            arrDetails.sort { (task1, task2) -> Bool in
                return task1.nStartTime > task2.nStartTime
            }
            
            // Sort array details based on date.
            arrDetails.sort { (task1, task2) -> Bool in
                return getDateFromString(strDate: task1.strDate) < getDateFromString(strDate:
                    task2.strDate)
            }
            return arrDetails
    }
    
    /// Set synch to server successful.
    func synchSuccessfull(taskId: Int) {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "task_id = %d", taskId)
        let nsMContext = try! nsManagedContext.fetch(fetchRequest)
        if nsMContext.count > 0 {
            //User finishes work.
            let nsMObject = nsMContext[0] as! NSManagedObject
            nsMObject.setValue(true, forKey: "is_synched")
            tasksTimeCDCtrlr.deletedOfflineTimes(taskId: taskId)
            let taskId = nsMObject.value(forKey: "task_id") as! Int
            if taskId < 0 {
                nsManagedContext.delete(nsMObject)
            }
            saveContext()
        }
        // Check for locally created tasks.
//        fetchRequest.predicate = NSPredicate(format: "task_id < %d", 0)
//        nsMContext = try! nsManagedContext.fetch(fetchRequest)
//        for nsMObject in nsMContext {
//            taskTimeUpdater.deletedOfflineTimes(taskId: taskId)
//            let taskId = (nsMObject as! NSManagedObject).value(forKey: "task_id") as! Int
//            if taskId < 0 {
//                nsManagedContext.delete(nsMObject as! NSManagedObject)
//            }
//            saveContext()
//        }
    }
    
    /// Locally stored task details (with is_synched value as true).
    func getLocallyStoredData() -> Array<TaskDetails> {
        var arrTaskDetails = Array<TaskDetails>()
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "is_synched = %d", 0)
        do {
            let nsMContext = try nsManagedContext.fetch(fetchRequest)
                for result in nsMContext {
                    let nsMObject = result as! NSManagedObject
                    let projId = nsMObject.value(forKey: "project_id") as! Int
                    let taskId = nsMObject.value(forKey: "task_id") as! Int
                    let modId = nsMObject.value(forKey: "module_id") as! Int
                    let taskName = nsMObject.value(forKey: "task_name") as! String
                    let taskDesc = nsMObject.value(forKey: "task_description") as! String
                    let totalTime = tasksTimeCDCtrlr.getTaskTotalTime(taskId: taskId)
                    let bWorking = nsMObject.value(forKey: "is_work_in_progress") as! Bool
                    
                    let nStartDateTime = nsMObject.value(forKey: "start_time") as! Int64
                    let nEndDateTime = nsMObject.value(forKey: "end_time") as! Int64
                    
                    let cTaskDetails = TaskDetails(taskId: taskId, taskName: taskName, taskDescr:
                        taskDesc, projId: projId, modId: modId, nTotalTime: totalTime,
                            nStartTime: nStartDateTime, nEndTime: nEndDateTime, isRunnung: bWorking)
                    
                    arrTaskDetails.append(cTaskDetails)
                }
        }
        catch {
            print("Error \(error)")
        }
        return arrTaskDetails
    }
    
    /// Check whether all tasks synched with server.
    func isSynched() -> Bool {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Tasks")
        fetchRequest.predicate = NSPredicate(format: "is_synched == %d", 0)
        let res = try! nsManagedContext.fetch(fetchRequest)
        return res.count > 0 ? false : true
    }
}
