/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : APIRequestHandler.swift
 //
 //    File Created      : 10:Dec:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : API Response handler.
 //
 //////////////////////////////////////////////////////////////////////////// */

import Foundation
import CoreData
import UIKit

class APIResponseHandler {
    
    /// Login authentication handler.
    static func login(email: String, password: String, completion:@escaping (Bool, String) -> ()) {
        let url = URL(string: "\(g_baseURL)\(g_subUrl)/login/")
        let params = ["username": email, "password": password]
        RequestController.requestToAPI(params: params, url: url!, completion: {
            dictResult in
            DispatchQueue.main.async {
                if 1 == dictResult["success"] as! Int {
                    let dictResLogin = dictResult["user_details"] as! Dictionary<String, Any>
//                    let serverLoginTime = dictResLogin["login_time"] as! String
//                    let localLogintime = convertAPILoginTimeToLocal(strDateTime: serverLoginTime)
                    if let strName = dictResLogin["username"] {
                        UserDefaults.standard.set(strName, forKey: "username")
                    }
                    if let authKey = dictResLogin["auth_key"] {
                        UserDefaults.standard.set(authKey, forKey: "userAuthKey")
                    }
                    if let userId = dictResLogin["id"] {
                        UserDefaults.standard.set(userId, forKey: "userId")
                    }
                    if let userProfUrl = dictResLogin["profile_pic"] as? String, userProfUrl != "" {
                        UserDefaults.standard.set(userProfUrl, forKey: "profileUrl")
                    }
                    if let phoneNo = dictResLogin["phone"] as? String, phoneNo != ""
                        , phoneNo != "0" {
                        UserDefaults.standard.set(phoneNo, forKey: "phoneNo")
                    }
                    completion(true, "")
                }
                else {
                    let strMsg = dictResult["msg"] as! String
                    completion(false, strMsg)
                }
            }
        })
    }
    
    /// Loads all projects from server to local core data.
    static func loadProjects(completion:@escaping (Bool) -> ()) {
        let container = NSPersistentContainer(name: "UserTaskDetails")
        print(container.persistentStoreDescriptions.first?.url as Any)
        let projectsCDController = ProjectsCDController()
        let moduleCDController = ModuleCDController()
        let url = URL(string: "\(g_baseURL)\(g_subUrl)/task/projects")
        if let authKey = UserDefaults.standard.value(forKey: "userAuthKey") {
            let strUserId = UserDefaults.standard.value(forKey: "userId") as! String
            let dictUserId = ["userid" : strUserId]
            RequestController.requestToAPI(params: dictUserId, url: url!, authKey: authKey
                as? String, completion: { dictResult in
                    // Load projects to core data.
                    DispatchQueue.main.async {
                        if 1 == dictResult["success"] as! Int {
                            let arrDictProject = dictResult["details"] as! Array<Any>
                            for dictProject in arrDictProject {
                                let dictProj = dictProject as! Dictionary<String, Any>
                                let projId = Int(dictProj["id"] as! String)
                                let projName = dictProj["project_name"] as! String
                                
                                var projIcon: String!
                                var projColor: String!
                                if let icon_url = dictProj["image_name"] as? String {
                                    projIcon = icon_url
                                }
                                else {
                                    projIcon = ""
                                }
                                if let color = dictProj["color_code"] as? String {
                                    projColor = color
                                }
                                else {
                                    projColor = "#d303fc"
                                }
                                
                                // Add or update project.
                                projectsCDController.addOrNewProject(projId: projId!, projectName:
                                    projName, projectIconUrl: projIcon, projColor: projColor)
                                
                                // Fetch modules.
                                let arrDictModules = dictProj["modules"] as!
                                    Array<Dictionary<String, String>>
                                for dictModule in arrDictModules {
                                    let modId = Int(dictModule["id"]!)
                                    let modName = dictModule["name"]!
                                    // Add modules to project.
                                    moduleCDController.addOrUpdateModule(modId: modId!, modName:
                                        modName, projId: projId!)
                                }
                                
                                // If any default modules add it.
                                if let arrDictDefaultMod = dictResult["default_module"] as?
                                    Array<Dictionary<String, String>> {
                                    for dictDefaultMod in arrDictDefaultMod {
                                        let modId = Int(dictDefaultMod["id"]!)
                                        let modName = dictDefaultMod["name"]!
                                        
                                        moduleCDController.addOrUpdateModule(modId: modId!,
                                            modName: modName, projId: projId!)
                                    }
                                }
                            }
                            // On complition of project load, update tasks details.
                            updateProjectDetails()
                            completion(true)
                        }
                        else {
                            print(dictResult["msg"]!)
                            completion(false)
                        }
                    }
            })
        }
    }
    
    
    /// Loads all task details from server to local core data.
    static func loadTaskDetails(pageNo: Int, completion:@escaping (Bool) -> ()) {
        let taskCDController = TasksCDController()
        let url = URL(string:
            "\(g_baseURL)\(g_subUrl)/task")
        if let authKey = UserDefaults.standard.value(forKey: "userAuthKey") {
            let strUserId = UserDefaults.standard.value(forKey: "userId") as! String
            
            let params = ["userid" : strUserId, "page_no": String(pageNo)]
            RequestController.requestToAPI(params: params, url: url!, authKey: authKey
                as? String, completion: { dictResult in
                    // Load tasks to core data.
                    DispatchQueue.main.async {
                        if 1 == dictResult["success"] as! Int {
                            g_totalPagesTask = (dictResult["total_pages"] as! Int)
                            g_taskCountInPage = (dictResult["task_per_page"] as! Int)
                            let arrDictTasks = dictResult["details"] as! Array<Any>
                            for dictTask in arrDictTasks {
                                
                                // Get all task informations.
                                let dictTask = dictTask as! Dictionary<String, Any>
                                let taskId = Int(dictTask["id"] as! String)
                                let taskName = dictTask["task_name"] as! String
                                let taskDesc = dictTask["description"] as! String
                                let projId = Int(dictTask["project_id"] as! String)
                                let modId = Int(dictTask["module_id"] as! String)
                                
                                // Get task timings.
                                let arrDictTaskTimings = dictTask["time_details"] as! Array<Any>
                                var nStartDate: Int64 = 0
                                var nEndDate: Int64 = 0
                                var bIsWorking = false
                                if arrDictTaskTimings.count > 0 {
                                    // Get start time from first task timings.
                                    let startTaskTime = arrDictTaskTimings[0]
                                        as! Dictionary<String, Any>
                                    let serverStartTime = startTaskTime["start_time"] as! String
                                    let localStartTime = convertAPITimeToLocal(strDateTime:
                                        serverStartTime)
                                    // Check for invalid data.
                                    if localStartTime == "invalid" {
                                        // Dont store that data.
                                        // Say invalide data.
                                        let alert = UIAlertController(title: "Alert"
                                            , message: "Some task timings stored in inavlid format. Please contact admin to fix it."
                                            , preferredStyle: UIAlertController.Style.alert)
                                        alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default
                                            , handler: {(_: UIAlertAction!) in
                                        }
                                        ))
                                        let presentingVC = UIApplication.topViewController()
                                        presentingVC!.present(alert, animated: true, completion: nil)
                                        continue
                                    }
                                    let startDate = convertUTCtoLocal(strDateTime:
                                        localStartTime)
                                    nStartDate = startDate.millisecondsSince1970
                                    
                                    // Check for task completed.
                                    if "1" == dictTask["completed"] as! String {
                                        // Get end time from last index.
                                        let endTaskTime = arrDictTaskTimings[arrDictTaskTimings
                                            .count-1] as! Dictionary<String, Any>
                                        let serverEndTime = endTaskTime["end_time"] as! String
                                        let localEndTime = convertAPITimeToLocal(strDateTime:
                                            serverEndTime)
                                        let endDate = convertUTCtoLocal(strDateTime:
                                            localEndTime)
                                        nEndDate = endDate.millisecondsSince1970
                                    }
                                    else {
                                        // If task not completed check for task in progress.
                                        let endTaskTime = arrDictTaskTimings[arrDictTaskTimings
                                            .count-1] as! Dictionary<String, Any>
                                        if nil == endTaskTime["end_time"] as? String {
                                            // If end time of last index not set the, work in progress
                                            bIsWorking = true
                                        }
                                    }
                                }
                                
                                // If task is not there adds it else updates details.
                                taskCDController.addOrUpdateTaskDetails(taskId: taskId!, taskName:
                                    taskName, taskDesc: taskDesc, projId: projId!, moduleId:
                                        modId!, bIsWorking: bIsWorking, startTime: nStartDate,
                                            endTime: nEndDate)
                                
                                // Load task timings.
                                loadTaskTimings(taskId: taskId!, arrDictValues: arrDictTaskTimings)
                            }
                            updateTaskDetails(pageNo: pageNo)
                            completion(true)
                        }
                        else {
                            print(dictResult["msg"]!)
                            completion(false)
                        }
                    }
            })
        }
    }
    
    /// Load task timings to core data.
    private static func loadTaskTimings(taskId: Int, arrDictValues: Array<Any>) {
        let taskTimeCDCtrlr = TasksTimeCDController()

        // Check for deleted time id from external logins.
        var arrTimeId: Array<Int> = []
        
        for dictValues in arrDictValues {
            let dictValues = dictValues as! Dictionary<String, Any>
            let timeId = Int(dictValues["id"] as! String)
            arrTimeId.append(timeId!)
            var taskDesc: String!
            if let descr = dictValues["task_description"] as? String {
                taskDesc = descr
            }
            else {
                taskDesc = ""
            }
            
            // Get task date, start time and end time.
            let serverStartDate = dictValues["start_time"] as! String
            let localStartTime = convertAPITimeToLocal(strDateTime: serverStartDate)
            // Check for invalid data.
            if localStartTime == "invalid" {
                // Dont store that data.
                // Say invalide data.
                let alert = UIAlertController(title: "Alert"
                    , message: "Some task timings stored in inavlid format. Please contact admin to fix it."
                    , preferredStyle: UIAlertController.Style.alert)
                alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default
                    , handler: {(_: UIAlertAction!) in
                }
                ))
                let presentingVC = UIApplication.topViewController()
                presentingVC!.present(alert, animated: true, completion: nil)
                continue
            }
            let startDate = convertUTCtoLocal(strDateTime: localStartTime)
            
            /// Get only date
            var strDate = String(localStartTime.split(separator: " ", maxSplits: 1
                , omittingEmptySubsequences: false)[0])
            strDate = convertStrDateFormate(strDate: strDate)
            let nStartTime = startDate.timeInDate
            
            // Check this block...!!!!!
            var nEndTime: Int = 0
            if let strEndDate = dictValues["end_time"] as? String {
                let serverEndTime = convertAPITimeToLocal(strDateTime: strEndDate)
                // Check for invalid data.
                if serverEndTime == "invalid" {
                    // Dont store that data.
                    continue
                }
                let localEndTime = convertUTCtoLocal(strDateTime: serverEndTime)
                nEndTime = localEndTime.timeInDate
            }
            // If that time exists updates information otherwise, add.
            taskTimeCDCtrlr.addOrUpdateTaskTimings(timeId: timeId!, taskId: taskId, strDate:
                strDate, startTime: nStartTime, endTime: nEndTime, descr: taskDesc)
        }
        
        // Check count of time details from server and coredata.
        if taskTimeCDCtrlr.getTimingsCount(of: taskId) > arrTimeId.count {
            let setServerTimes = Set(arrTimeId)
            let setCoreDataTimes = Set(taskTimeCDCtrlr.getTimingsId(of: taskId))
            let setToDelete = setCoreDataTimes.subtracting(setServerTimes)
            for timeId in setToDelete {
                taskTimeCDCtrlr.deleteTaskTime(timeId: timeId)
            }
        }
    }
    
    /// To add or edit task.0
    static func addTask(params: Dictionary<String, Any>, completion:@escaping (Bool, Int, String)
        -> ()) {
        let url = URL(string:
            "\(g_baseURL)\(g_subUrl)/task/create_edit")
        if let authKey = UserDefaults.standard.value(forKey: "userAuthKey") {
            RequestController.requestToAPI(params: params, url: url!, authKey: authKey
                as? String, completion: { dictResult in
                    DispatchQueue.main.async {
                        let strMsg = dictResult["msg"] as! String
                        if 1 == dictResult["success"] as! Int {
                            if let taskId = dictResult["task_id"] as? Int {
                                // If new task created.
                                completion(true, taskId, strMsg)
                            }
                            else {
                                completion(true, 0, strMsg)
                            }
                        }
                        else {
                            completion(false, 0, strMsg)
                        }
                    }
                    
            })
        }
    }
    
    /// To start punch in or task. (If taskId is nil, function works as punch in updator else task time starter)
    static func startTaskOrPunchIn(taskId: String? = nil, time: String? = nil
        , completion:@escaping (Bool, String) -> ()) {
        let url = URL(string: "\(g_baseURL)\(g_subUrl)/task/start_timer")
        if let authKey = UserDefaults.standard.value(forKey: "userAuthKey") {
            let strUserId = UserDefaults.standard.value(forKey: "userId") as! String
            // Handling start task.
            if nil != taskId {
                let params = ["userid": strUserId, "type": "task", "task_id": taskId!]
                RequestController.requestToAPI(params: params, url: url!, authKey: authKey
                    as? String, completion: { dictResult in
                        // Load projects to core data.
                        DispatchQueue.main.async {
                            let strMsg = dictResult["msg"] as! String
                            if 1 == dictResult["success"] as! Int {
                                completion(true, strMsg)
                            }
                            else {
                                print(dictResult["msg"]!)
                                completion(false, strMsg)
                            }
                        }
                })
            }
                // Handling punchin time.
            else {
                let params = ["userid": strUserId, "type": "login", "start_time": time!]
                RequestController.requestToAPI(params: params, url: url!, authKey: authKey
                    as? String, completion: { dictResult in
                        DispatchQueue.main.async {
                            let strMsg = dictResult["msg"] as! String
                            if 1 == dictResult["success"] as! Int {
                                completion(true, strMsg)
                            }
                            else {
                                print(dictResult["msg"]!)
                                completion(false, strMsg)
                            }
                        }
                })
            }
        }
    }
    
    /// To stop task or update punch out time. (If taskId is nil, function works as punch out updator else task time stoper)
    static func stopTaskOrPunchIn(taskId: String? = nil, completion:@escaping (Bool) -> ()) {
        let url = URL(string: "\(g_baseURL)\(g_subUrl)/task/stop_timer")
        if let authKey = UserDefaults.standard.value(forKey: "userAuthKey") {
            let strUserId = UserDefaults.standard.value(forKey: "userId") as! String
            
            // Handling stop task.
            if nil != taskId {
                let params = ["userid": strUserId, "type": "task", "task_id": taskId!]
                RequestController.requestToAPI(params: params, url: url!, authKey: authKey
                    as? String, completion: { dictResult in
                        DispatchQueue.main.async {
                            if 1 == dictResult["success"] as! Int {
                                completion(true)
                            }
                            else {
                                print(dictResult["msg"]!)
                                completion(false)
                            }
                        }
                })
            }
                // Handling punchout time.
            else {
                let params = ["userid": strUserId, "type": "login"]
                RequestController.requestToAPI(params: params, url: url!, authKey: authKey
                    as? String, completion: { dictResult in
                        // Load projects to core data.
                        DispatchQueue.main.async {
                            if 1 == dictResult["success"] as! Int {
                                completion(true)
                            }
                            else {
                                print(dictResult["msg"]!)
                                completion(false)
                            }
                        }
                })
            }
        }
    }
    
    /// Load punch in/out data to coredata.
    static func loadPunchInOut(pageNo: Int, completion:@escaping (Bool) -> ()) {
        let punchInOutCDCtrlr = PunchInOutCDController()
        let url = URL(string: "\(g_baseURL)\(g_subUrl)/login/details")
        if let authKey = UserDefaults.standard.value(forKey: "userAuthKey") {
            let strUserId = UserDefaults.standard.value(forKey: "userId") as! String
            let params = ["userid": strUserId, "page_no": String(pageNo)]
            RequestController.requestToAPI(params: params, url: url!, authKey: authKey
                as? String, completion: { dictResult in
                    DispatchQueue.main.async {
                        if 1 == dictResult["success"] as! Int {
                            g_totalPagesPunchInOut = (dictResult["total_pages"] as! Int)
                            g_punchInOutCountInPage = (dictResult["records_per_page"] as! Int)
                            let arrDictTimings = dictResult["details"] as! Array<Any>
                            for dictTime in arrDictTimings {
                                
                                // Get punch in and out timings.
                                let dictTask = dictTime as! Dictionary<String, Any>
                                let startTime = dictTask["start_time"] as! String
                                let endTime = dictTask["end_time"] as? String
                                _ = dictTask["task_date"] as! String
                                
                                // Convert to local time.
                                let localStartTime = convertAPITimeToLocal(strDateTime: startTime)
                                let startDate = convertUTCtoLocal(strDateTime: localStartTime)
                                    .millisecondsSince1970
                                
                                var endDate: Int64!
                                if nil != endTime {
                                    let localEndTime = convertAPITimeToLocal(strDateTime: endTime!)
                                    endDate = convertUTCtoLocal(strDateTime: localEndTime)
                                        .millisecondsSince1970
                                }
                                punchInOutCDCtrlr.addOrUpdatePunchInOutTime(start: startDate, end: endDate)
                            }
                            completion(true)
                        }
                        else {
                            print(dictResult["msg"]!)
                            completion(false)
                        }
                    }
            })
        }
    }
    
    /// Update punch out timings to server. (Previous day's)
    static func updatePunchOutTimings(date: String, time: String, completion:@escaping (Bool, String)
        -> ()) {
        let url = URL(string: "\(g_baseURL)\(g_subUrl)/task/update_time")
        if let authKey = UserDefaults.standard.value(forKey: "userAuthKey") {
            let strUserId = UserDefaults.standard.value(forKey: "userId") as! String
            let params = ["userid": strUserId, "type": "login", "date": date, "time": time]
            RequestController.requestToAPI(params: params, url: url!, authKey: authKey
                as? String, completion: { dictResult in
                    DispatchQueue.main.async {
                        let strMsg = dictResult["msg"] as! String
                        if 1 == dictResult["success"] as! Int {
                            completion(true, strMsg)
                        }
                        else {
                            print(dictResult["msg"]!)
                            completion(false, strMsg)
                        }
                    }
            })
        }
    }
    
    /// Update task timings to server. (Previous day's)
    static func updateTaskTimings(taskId: Int, date: String, time: String
        , completion:@escaping (Bool, String) -> ()) {
        let url = URL(string: "\(g_baseURL)\(g_subUrl)/task/update_time")
        if let authKey = UserDefaults.standard.value(forKey: "userAuthKey") {
            let strUserId = UserDefaults.standard.value(forKey: "userId") as! String
            let params = ["userid": strUserId, "task_id": String(taskId), "type": "task", "date":
                date, "time": time]
            RequestController.requestToAPI(params: params, url: url!, authKey: authKey
                as? String, completion: { dictResult in
                    let strMsg = dictResult["msg"] as! String
                    DispatchQueue.main.async {
                        if 1 == dictResult["success"] as! Int {
                            completion(true, strMsg)
                        }
                        else {
                            print(strMsg)
                            completion(false, strMsg)
                        }
                    }
            })
        }
    }
    
    /// When internet connected to the device call this method to synch task details with server.
    static func updateLocalTasksToServer() {
        let taskCDCtrlr = TasksCDController()
        let arrTaskDetails = taskCDCtrlr.getLocallyStoredData()
        
        for taskDetails in arrTaskDetails {
            /// Create API parameter.
            var dictParams = Dictionary<String, Any>()
            let strUserId = UserDefaults.standard.value(forKey: "userId") as! String
            dictParams.updateValue(strUserId, forKey: "userid")
            if taskDetails.taskId! > 0 {
                dictParams.updateValue("\(taskDetails.taskId!)", forKey: "task_id")
            }
            dictParams.updateValue(taskDetails.taskName!, forKey: "task_name")
            dictParams.updateValue(taskDetails.taskDescription!, forKey: "task_desc")
            dictParams.updateValue("\(taskDetails.projId!)", forKey: "project_id")
            dictParams.updateValue("\(taskDetails.moduleId!)", forKey: "project_module")
            
            let arrayTaskTimings = taskDetails.arrTaskTimings
            var arrDictTimings = Array<Any>()
            if arrayTaskTimings.count > 0 {
                var index = 0
                for cTaskTimeDetails in arrayTaskTimings {
                    let strDate = cTaskTimeDetails.strDate!
                    
                    let nStartTime = cTaskTimeDetails.nStartTime!
                    
                    let nEndTime = cTaskTimeDetails.nEndTime!
                    let strStartTime = getSecondsToHoursMinutesSeconds(seconds: nStartTime)
                    let strEndTime = getSecondsToHoursMinutesSeconds(seconds: nEndTime)
                    
                    let strDateToAPI = convertLocalDateToUTC(strDate: "\(strDate) \(strStartTime)"
                        , format: "dd/MM/yyyy HH:mm:ss")
                    let strStartDateTime = convertLocalTimeToUTC(strDateTime:
                        "\(strDate) \(strStartTime)")
                    var strEndDateTime = convertLocalTimeToUTC(strDateTime:
                        "\(strDate) \(strEndTime)")
                    let descriptn = cTaskTimeDetails.description ?? ""
                    
                    if taskDetails.bIsRunning! && index == arrayTaskTimings.count-1 {
                        strEndDateTime = ""
                    }

                    var dictTimings: Dictionary<String, Any>!
                    if cTaskTimeDetails.timeId > 0 {
                        // If time id id from server DB.
                        // Edit task timings.
                        dictTimings = ["date":strDateToAPI, "start": strStartDateTime,
                                       "end": strEndDateTime,"task_description": descriptn,
                                       "table_id": "\(cTaskTimeDetails.timeId!)"]
                    }
                    else {
                        // Otherwise, append new task time to server db.
                        dictTimings = ["date":strDateToAPI,"start":strStartDateTime,"end":
                            strEndDateTime, "task_description":descriptn]
                        
                        // If task is running send only start time.
                        if taskDetails.bIsRunning! && index == arrayTaskTimings.count-1 {
                            dictTimings["end"] = ""
                        }
                    }
                    do {
                        var strData = "{"
                        for (key, value) in dictTimings {
                            strData += "\(key):\(value),"
                        }
                        strData += "}"
                        
                        arrDictTimings.append(dictTimings!)
                    }
                    index += 1
                }
            }
            dictParams.updateValue(arrDictTimings, forKey: "time_range")
            if dictParams.count > 0 {
                APIResponseHandler.addTask(params: dictParams, completion: {
                    (status, id, msg)  in
                    if status {
                        print("Uploaded offline data to server")
                        taskCDCtrlr.synchSuccessfull(taskId: taskDetails.taskId!)
                        let appNotif = InAppNotificationView()
                        appNotif.sendNotification(msg
                            : "Offline data successfully loaded to the server")
                        appNotif.addGradient()
                        if var topController = UIApplication.shared.keyWindow?.rootViewController {
                            while let presentedViewController = topController
                                .presentedViewController {
                                topController = presentedViewController
                            }
                            topController.view.addSubview(appNotif)
                        }
                    }
                    else {
                        print("Not updated offline data.. \(msg)")
                    }
                })
            }
        }
    }
    
    /// To reset password from current password.
    static func resetPassword(oldPW: String, newPW: String, completion: @escaping (Bool, String)
        -> ()) {
        let url = URL(string: "\(g_baseURL)\(g_subUrl)/user/changepassword")
        if let authKey = UserDefaults.standard.value(forKey: "userAuthKey") {
            let strUserId = UserDefaults.standard.value(forKey: "userId") as! String
            let params = ["userid": strUserId, "old_password": oldPW, "new_password": newPW]
            RequestController.requestToAPI(params: params, url: url!, authKey: authKey
                as? String, completion: {
                    dictResult in
                    DispatchQueue.main.async {
                        let strMsg = dictResult["msg"] as! String
                        if 1 == dictResult["success"] as! Int {
                            completion(true, strMsg)
                        }
                        else {
                            completion(false, strMsg)
                        }
                    }
            })
        }
    }
    
    /// To send OTP to the user's email address.
    static func sendOTP(email: String, completion:@escaping (Bool, String) -> ()) {
        let url = URL(string: "\(g_baseURL)\(g_subUrl)/user/send_otp")
        let params = ["email": email]
        RequestController.requestToAPI(params: params, url: url!, completion: {
                dictResult in
                DispatchQueue.main.async {
                    let strMsg = dictResult["msg"] as! String
                    if 1 == dictResult["success"] as! Int {
                        completion(true, strMsg)
                    }
                    else {
                        completion(false, strMsg)
                    }
                }
        })
    }
    
    static func validateOTP(email: String, otp: String, completion:@escaping (Bool, String) -> ()) {
        let url = URL(string: "\(g_baseURL)\(g_subUrl)/user/validate_otp")
        let params = ["email": email, "otp": otp]
        RequestController.requestToAPI(params: params, url: url!, completion: {
            dictResult in
            DispatchQueue.main.async {
                let strMsg = dictResult["msg"] as! String
                if 1 == dictResult["success"] as! Int {
                    completion(true, strMsg)
                }
                else {
                    completion(false, strMsg)
                }
            }
        })
    }
    
    static func resetOtpPassword(newPW: String, completion:@escaping (Bool, String) -> ()) {
        let url = URL(string: "\(g_baseURL)\(g_subUrl)/user/resetpassword")
        let email = UserDefaults.standard.value(forKey: "userEmail") as! String
        let params = ["email": email, "new_password": newPW]
        RequestController.requestToAPI(params: params, url: url!, completion: {
            dictResult in
            DispatchQueue.main.async {
                let strMsg = dictResult["msg"] as! String
                if 1 == dictResult["success"] as! Int {
                    completion(true, strMsg)
                }
                else {
                    completion(false, strMsg)
                }
            }
        })
    }
    
    static func updateUserProfile(name: String, phone: String, imgData: Data
        , completion:@escaping (Bool, String) -> ()) {
        let url = URL(string: "\(g_baseURL)\(g_subUrl)/user/edit_profile")
        if let authKey = UserDefaults.standard.value(forKey: "userAuthKey") {
            let strUserId = UserDefaults.standard.value(forKey: "userId") as! String
            let params = ["userid": strUserId, "name": name, "phone": phone, "image_data": imgData]
                as [String : Any]
            RequestController.requestToAPI(params: params, url: url!, authKey: authKey
                as? String, completion: {
                    dictResult in
                    DispatchQueue.main.async {
                        let strMsg = dictResult["msg"] as! String
                        if 1 == dictResult["success"] as! Int {
                            completion(true, strMsg)
                        }
                        else {
                            completion(false, strMsg)
                        }
                    }
            })
        }
    }
    
    static func fetchUserProfile(completion: @escaping (Bool, String) -> ()) {
        let url = URL(string: "\(g_baseURL)\(g_subUrl)/user/fetch_profile")
        if let authKey = UserDefaults.standard.value(forKey: "userAuthKey") {
            let strUserId = UserDefaults.standard.value(forKey: "userId") as! String
            let params = ["userid": strUserId]
            RequestController.requestToAPI(params: params, url: url!, authKey: authKey
                as? String, completion: {
                    dictResult in
                    DispatchQueue.main.async {
                        if 1 == dictResult["success"] as! Int {
                            let dictResLogin = dictResult["details"] as! Dictionary<String, Any>
                            if let strName = dictResLogin["name"] {
                                UserDefaults.standard.set(strName, forKey: "username")
                            }
                            if let userProfUrl = dictResLogin["profile_pic"] as? String
                                , userProfUrl != "" {
                                UserDefaults.standard.set(userProfUrl, forKey: "profileUrl")
                            }
                            if let phoneNo = dictResLogin["phone"] as? String, phoneNo != ""
                                , phoneNo != "0" {
                                UserDefaults.standard.set(phoneNo, forKey: "phoneNo")
                            }
                            completion(true, "User Profile loaded")
                        }
                        else {
                            let strMsg = dictResult["msg"] as! String
                            completion(false, strMsg)
                        }
                    }
            })
        }
    }
}
