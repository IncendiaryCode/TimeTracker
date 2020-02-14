/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : TimeUpdateView.swift
 //
 //    File Created      : 26:Dec:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : If previous day task or punch out time not updated
 //                        use this view to update it.
 //
 //////////////////////////////////////////////////////////////////////////// */

import UIKit

enum TaskType {
    case login
    case prevDayPunchOut
    case todayPunchOut
    case task
}

class TimeUpdateView: UIView {
    var viewContainer: UIView!
    var lblHeader: UILabel!
    var lblDesc: UILabel!
    var timePicker: UIDatePicker!
    var btnSave: UIButton!
    var actIndicator: UIActivityIndicatorView!
    var lblError: UILabel!
    
    var date: String!
    var startTime: String!
    var taskName: String!
    var taskId: Int!
    var type: TaskType!
    var timeId: Int!
    
    /// Add completion handler, which will be called after completion of saved.
    var completionHandler: (Bool) -> Void = {_ in }
    
    /// Nib name
    let nibName = "PrevDayTimeAdder"
    
    required init?(coder aDecoder: NSCoder) {
        super.init(coder: aDecoder)
        initLoad()
    }
    
    func initLoad() {
        let screenBounds = UIScreen.main.bounds
        self.frame = screenBounds
        backgroundColor = UIColor.clear.withAlphaComponent(0.5)
        
        // View container.
        var cgRect = CGRect(x: 0, y: screenBounds.height*0.3, width: screenBounds.width
            , height: screenBounds.height - screenBounds.height*0.3+25) // 20 to hide corner radius
        viewContainer = UIView(frame: cgRect)
        viewContainer.backgroundColor = g_colorMode.defaultColor()
        viewContainer.layer.cornerRadius = 35
        viewContainer.layer.masksToBounds = true
        self.addSubview(viewContainer)
        
        // header setup
        cgRect = CGRect(x: 0, y: 0, width: screenBounds.width, height: 50)
        lblHeader = UILabel(frame: cgRect)
        lblHeader.text = "Update Time"
        lblHeader.font = lblHeader.font.withSize(18)
        lblHeader.textColor = g_colorMode.textColor()
        lblHeader.textAlignment = .center
        lblHeader.drawShadow()
        viewContainer.addSubview(lblHeader)
        
        // lblDesc setup
        cgRect = CGRect(x: 20, y: lblHeader.frame.maxY, width: screenBounds.width-40, height: 90)
        lblDesc = UILabel(frame: cgRect)
        lblDesc.font = lblHeader.font.withSize(18)
        lblDesc.numberOfLines = 3
        lblDesc.textColor = g_colorMode.textColor()
        viewContainer.addSubview(lblDesc)
        
        // Setup date picker.
        cgRect = CGRect(x: 20, y: lblDesc.frame.maxY, width: screenBounds.width-40
            , height: viewContainer.bounds.height - lblDesc.frame.maxY - 110)
        timePicker = UIDatePicker(frame: cgRect)
        timePicker.setValue(g_colorMode.textColor(), forKeyPath: "textColor")
        viewContainer.addSubview(timePicker)
        
        // Label Error.
        cgRect = CGRect(x: 20, y: timePicker.frame.maxY+20, width: screenBounds.width - 200
            , height: 20)
        lblError = UILabel(frame: cgRect)
        lblError.layer.masksToBounds = true
        lblError.layer.cornerRadius = 10
        lblError.isHidden = true
        lblError.textAlignment = .center
        lblError.textColor = .white
        lblError.layer.masksToBounds = true
        lblError.layer.cornerRadius = 10
        lblError.font = lblError.font.withSize(12)
        lblError.backgroundColor = .red
        viewContainer.addSubview(lblError)
        
        // Save button.
        cgRect = CGRect(x: screenBounds.width - 120, y: timePicker.frame.maxY+25, width: 100
            , height: 44)
        btnSave = UIButton(frame: cgRect)
        btnSave.addTarget(self, action: #selector(btnSavePressed(_:)), for: .touchUpInside)
        btnSave.center = CGPoint(x: btnSave.frame.midX, y: lblError.frame.midY)
        let cgPStart = CGPoint(x: 0, y: 0.25)
        let cgPEnd = CGPoint(x: 1, y: 0.75)
        btnSave.addGradient(cgPStart: cgPStart, cgPEnd: cgPEnd, cgFRadius: 15)
        btnSave.setTitle("Save", for: .normal)
        viewContainer.addSubview(btnSave)
        
        /// Act Controller.
        cgRect = CGRect(x: 0, y: 0, width: 40, height: 40)
        actIndicator = UIActivityIndicatorView(frame: cgRect)
        actIndicator.center = btnSave.center
        actIndicator.hidesWhenStopped = true
        viewContainer.addSubview(actIndicator)
    }
    
    override init(frame: CGRect) {
        super.init(frame: frame)
        initLoad()
    }
    
    /// Custome init to setup all required data. (Must call after init with frame)
    public func customInit(date: String, time: String, taskName: String, taskId: Int,
                           timeId: Int? = 0, type: TaskType) {
        self.date = date
        self.startTime = time
        self.taskName = taskName
        self.taskId = taskId
        self.timeId = timeId
        self.type = type
        
        // Set min date.
        let minDate = convertStrDateTimeToDate(strDateTime: "\(date) \(time)")
        if type == .login {
            
            // Set max date to current time.
            timePicker.maximumDate = Date()
            timePicker.minimumDate = convertStrDateTimeToDate(strDateTime: "\(date) 00:00:00")
        }
        else {
            timePicker.minimumDate = minDate
            // Set maximum to 23:59.
            let maxDate = convertStrDateTimeToDate(strDateTime: "\(date) 23:59:00")
            timePicker.maximumDate = maxDate
        }
        
        lblHeader.textColor = g_colorMode.textColor()
        lblDesc.textColor = g_colorMode.textColor()
        
        if type == .task {
            let punchInOutCDTrlr = PunchInOutCDController()
            let (_, endTime) = punchInOutCDTrlr.getPunchInAndOutTime()
            timePicker.minimumDate = minDate
            timePicker.maximumDate = endTime
            lblHeader.text = "Update Task"
            
            
            lblDesc.text = "A task \(taskName) is not ended for the date \(date). Please enter the end time with in the punch out time: \(endTime?.getStrTime() ?? "no value")"
        }
        else if type == .login {
            lblHeader.text = "Update Task"
            lblDesc.text = "Please submit your punch in time"
        }
        else if type == .prevDayPunchOut {
            lblHeader.text = "Update"
            lblDesc.text = "Please submit your punch out time on \(date).\nPunched in time: \(time)"
        }
        else {
            lblHeader.text = "Update"
            lblDesc.text = "Please submit your punch out time.\nPunched in time: \(time)"
        }
    }
    
    /// If any tasks time are beyond punch out time.
    private func isTaskBeyondPunchOutUpdate(arrTaskId: Array<Int>) {
        var str = String()
        
        if arrTaskId.count > 1 {
            str = "tasks"
        } else {
            str = "task"
        }
        let alert = UIAlertController(title: "Alert"
            , message: "\(arrTaskId.count) \(str) time line is beyond punch out time. Do you want to update it to current punch out time?",
              preferredStyle: UIAlertController.Style.alert)
        alert.addAction(UIAlertAction(title: "Yes", style: UIAlertAction.Style.default,
                                      handler: {(_: UIAlertAction!) in
                                        //Office Leave action
                                        self.updateTaskEndTimings(arrTaskId: arrTaskId)
        }
        ))
        alert.addAction(UIAlertAction(title: "No", style: UIAlertAction.Style.cancel, handler:
            { _ in
                self.removeFromSuperview()
        }))
        
        let presentingVC = UIApplication.topViewController()
        presentingVC!.present(alert, animated: true, completion: nil)
    }
    
    /// Check any task timings beyond punch out before punch out.
    private func updateTaskEndTimings(arrTaskId: Array<Int>) {
        actIndicator.startAnimating()
        for taskId in arrTaskId {
            let taskTimeCDCtrlr = TasksTimeCDController()
            let arrTaskTimeDetails: Array<TaskTimeDetails> = taskTimeCDCtrlr
                .getTaskTimes(taskId: taskId)
            var dictParams = Dictionary<String, Any>()
            var arrDictTimings = Array<Any>()
            var arrDelTimeId = Array<String>()
            
            for cTaskTimeDetails in arrTaskTimeDetails {
                let strDate = cTaskTimeDetails.strDate!
                
                let nStartTime = cTaskTimeDetails.nStartTime!
                var nEndTime = cTaskTimeDetails.nEndTime!
                let pickerDate = timePicker.date
                let timeInDate = pickerDate.timeInDate
                
                if nEndTime > timeInDate {
                    // Check start time is greater than punch out.
                    if nStartTime > timeInDate {
                        // Delete those timings.
                        if cTaskTimeDetails.timeId! > 0 {
                            arrDelTimeId.append("\(cTaskTimeDetails.timeId!)")
                        }
                        else {
                            taskTimeCDCtrlr.deleteTaskTime(timeId: cTaskTimeDetails.timeId!)
                        }
                        continue
                    }
                    else {
                        nEndTime = timeInDate
                    }
                }
                else {
                    continue
                }
                let strStartTime = getSecondsToHoursMinutesSeconds(seconds: nStartTime)
                
                let strDateToAPI = convertLocalDateToUTC(strDate: "\(strDate) \(strStartTime)"
                    , format: "dd/MM/yyyy HH:mm:ss")
                let strStartDateTime = convertLocalTimeToUTC(strDateTime: "\(strDate) \(strStartTime)")
                
                let strEndTime = getSecondsToHoursMinutesSeconds(seconds: nEndTime)
                let strEndDateTime = convertLocalTimeToUTC(strDateTime: "\(strDate) \(strEndTime)")
                
                let descriptn = cTaskTimeDetails.description!
                
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
                    dictTimings = ["date": strDateToAPI, "start": strStartDateTime, "end"
                        : strEndDateTime, "task_description": descriptn]
                }
                arrDictTimings.append(dictTimings!)
            }
            let strUserId = UserDefaults.standard.value(forKey: "userId") as! String
            dictParams.updateValue(strUserId, forKey: "userid")
            
            let taskDetails = getTaskDetails(taskId: taskId)!
            
            dictParams.updateValue("\(taskId)", forKey: "task_id")
            dictParams.updateValue("\(taskDetails.taskName!)", forKey: "task_name")
            dictParams.updateValue("\(taskDetails.taskDescription!)", forKey: "task_desc")
            dictParams.updateValue("\(taskDetails.projId!)", forKey: "project_id")
            dictParams.updateValue("\(taskDetails.moduleId!)", forKey: "project_module")
            dictParams.updateValue(arrDelTimeId, forKey: "deleted_time_range")
            dictParams.updateValue(arrDictTimings, forKey: "time_range")
            
            // Update Edited task timings.
            APIResponseHandler.addTask(params: dictParams, completion: {
                (status, id, msg) in
                if status {
                    // If any timings removed then, remove it from core data too.
                    for timeId in arrDelTimeId {
                        taskTimeCDCtrlr.deleteTaskTime(timeId: Int(timeId)!)
                    }
                    let date = self.timePicker.date
                    let strTime = date.getStrTime()
                    let apiDate = convertLocalDateToUTC(strDate: "\(self.date!) \(strTime)"
                        , format: "dd/MM/yyyy h:mm a")
                    let apiTime = convertLocalTimeToUTC(strTime: strTime)
                    self.updatePunchOut(apiDate: apiDate, apiTime: apiTime)
                }
                else {
                    print("Error while updating")
                    self.lblError.isHidden = false
                    self.lblError.text = "Updating punch out is got failed."
                }
            })
        }
    }
    
    private func updatePunchOut(apiDate: String, apiTime: String) {
        btnSave.setTitle("", for: .normal)
        lblError.isHidden = true
        actIndicator.startAnimating()
        //Update end time API.
        
        APIResponseHandler.updatePunchOutTimings(date: apiDate, time: apiTime, completion: {
            status, msg in
            if status {
                APIResponseHandler.loadPunchInOut(pageNo: 1, completion: {
                    status in
                    if status {
                        print("Punch out time with todays data loaded..!")
                    }
                    else {
                        print("Punch out time with todays data not loaded..!")
                    }
                    self.completionHandler(status)
                    self.removeFromSuperview()
                })
                print("Punch out updates successfully..")
            }
            else {
                print("Punch out updation failed..!")
                self.lblError.text = msg
                self.lblError.isHidden = false
                self.actIndicator.stopAnimating()
                self.btnSave.setTitle("Save", for: .normal)
            }
        })
    }
    
    private func updatePunchIn(apiDate: String, apiTime: String) {
        btnSave.setTitle("", for: .normal)
        lblError.isHidden = true
        actIndicator.startAnimating()
        //Update end time API.
        APIResponseHandler.startTaskOrPunchIn(time: "\(apiDate) \(apiTime)", completion: {
            status, msg  in
            if status {
                print("Punch in time updated..!")
                APIResponseHandler.loadPunchInOut(pageNo: 1, completion: {
                    status in
                    if status {
                        g_isPunchedIn = true
                        print("Punch in time with todays data loaded..!")
                    }
                    else {
                        print("Punch in time with todays data not loaded..!")
                    }
                    self.completionHandler(status)
                    self.removeFromSuperview()
                })
            }
            else {
                self.lblError.text = msg
                self.lblError.isHidden = false
                print("Punch in time not updated..!")
                self.actIndicator.stopAnimating()
                self.btnSave.setTitle("Save", for: .normal)
            }
        })
    }
    
    @objc func btnSavePressed(_ sender: Any) {
        let date = timePicker.date
        let strTime = date.getStrTime()
        let apiDate = convertLocalDateToUTC(strDate: "\(self.date!) \(strTime)"
            , format: "dd/MM/yyyy h:mm a")
        let apiTime = convertLocalTimeToUTC(strTime: strTime)
        
        if type == .prevDayPunchOut {
            //Alert while puch in.
            let alert = UIAlertController(title: "Alert"
                , message: "Are you sure, your punch out time is updated to \(self.date!) \(strTime)?",
                                          preferredStyle: UIAlertController.Style.alert)
            alert.addAction(UIAlertAction(title: "Yes", style: UIAlertAction.Style.default,
                                          handler: {(_: UIAlertAction!) in
                                            self.updatePunchOut(apiDate: apiDate, apiTime: apiTime)
            }
            ))
            alert.addAction(UIAlertAction(title: "No", style: UIAlertAction.Style.cancel, handler:
                { _ in
                    self.removeFromSuperview()
            }))
            
            // get top most vc.
            let viewController = UIApplication.topViewController()
            viewController!.present(alert, animated: true, completion: nil)
        }
        else if type == .login {
            //Alert while puch in.
            let alert = UIAlertController(title: "Alert"
                , message: "Are you sure, your punch in time is updated to \(self.date!) \(strTime)?"
                ,preferredStyle: UIAlertController.Style.alert)
            alert.addAction(UIAlertAction(title: "Yes", style: UIAlertAction.Style.default,
                                          handler: {(_: UIAlertAction!) in
                                            self.updatePunchIn(apiDate: apiDate, apiTime: apiTime)
            }
            ))
            alert.addAction(UIAlertAction(title: "No", style: UIAlertAction.Style.cancel, handler:
                { _ in
                    self.removeFromSuperview()
            }))
            // get top most vc.
            let viewController = UIApplication.topViewController()
            viewController!.present(alert, animated: true, completion: nil)
        }
        else if type == .task {
            btnSave.setTitle("", for: .normal)
            lblError.isHidden = true
            actIndicator.startAnimating()
            APIResponseHandler.updateTaskTimings(taskId: self.taskId, date: apiDate, time: apiTime
                , completion: {
                status, msg in
                if status {
                    print("Task updated successfully..")
                    
                    // Update end time in core data.
                    let taskTimeCDTrlr = TasksTimeCDController()
                    let date = date.getStrDate()
                    taskTimeCDTrlr.updateDateTimings(timeId: self.timeId, date: date
                        , startTime: getSecondCountFormat2(strTime: self.startTime)
                        , endTime: self.timePicker.date.timeInDate, descr: "")

                    self.completionHandler(status)
                    self.removeFromSuperview()
                }
                else {
                    print("Task updation failed..!")
                    self.lblError.text = msg
                    self.lblError.isHidden = false
                    self.actIndicator.stopAnimating()
                    self.btnSave.setTitle("Save", for: .normal)
                }
            })
        }
            // Today's punch out.
        else {
            let alert = UIAlertController(title: "Alert"
                , message: "Are you sure, your punch out time is updated to \(self.date!) \(strTime)?",
                preferredStyle: UIAlertController.Style.alert)
            alert.addAction(UIAlertAction(title: "Yes", style: UIAlertAction.Style.default
                ,handler: {(_: UIAlertAction!) in
                    // If any tasks time beyond current time.
                    let taskTimeCDCtrlr = TasksTimeCDController()
                    let arrTaskId = taskTimeCDCtrlr.tasksBeyondCurrentTime()
                    if arrTaskId.count > 0 {
                        self.isTaskBeyondPunchOutUpdate(arrTaskId: arrTaskId)
                    }
                    else {
                        let date = self.timePicker.date
                        let strTime = date.getStrTime()
                        let apiDate = convertLocalDateToUTC(strDate: "\(self.date!) \(strTime)"
                            , format: "dd/MM/yyyy h:mm a")
                        let apiTime = convertLocalTimeToUTC(strTime: strTime)
                        self.updatePunchOut(apiDate: apiDate, apiTime: apiTime)
                    }
                }
            ))
            alert.addAction(UIAlertAction(title: "No", style: UIAlertAction.Style.cancel, handler:
                { _ in
                    self.removeFromSuperview()
            }))
            
            // get top most vc.
            let viewController = UIApplication.topViewController()
            viewController!.present(alert, animated: true, completion: nil)
        }
    }

}
