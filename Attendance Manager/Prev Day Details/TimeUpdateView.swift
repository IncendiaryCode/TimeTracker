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
    case logout
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
    var completionHandler: () -> Void = {}
    
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
        lblDesc.numberOfLines = 2
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
            timePicker.minimumDate = convertStrDateTimeToDate(strDateTime: "\(date) 08:00:00")
            
            // Set max date to current time.
            timePicker.maximumDate = Date()
        }
        else {
            timePicker.minimumDate = minDate
            // Set maximum to 8PM.
            let maxDate = convertStrDateTimeToDate(strDateTime: "\(date) 20:00:00")
            timePicker.maximumDate = maxDate
        }
        
        lblHeader.textColor = g_colorMode.textColor()
        lblDesc.textColor = g_colorMode.textColor()
        
        if type == .task {
            lblHeader.text = "Update Task"
            lblDesc.text = "A task \(taskName) is not ended for the \(date). Please enter the end time"
        }
        else if type == .login {
            lblHeader.text = "Update Task"
            lblDesc.text = "Please submit your punch in time"
        }
        else {
            lblHeader.text = "Update"
            lblDesc.text = "Please submit your punch out time on \(date)."
        }
    }
    
    @objc func btnSavePressed(_ sender: Any) {
        lblError.isHidden = true
        actIndicator.startAnimating()
        //Update end time API.
        
        let date = timePicker.date
        let strTime = date.getStrTime()
        let apiDate = convertLocalDateToUTC(strDate: "\(self.date!) \(strTime)"
            , format: "dd/MM/yyyy h:mm a")
        let apiTime = convertLocalTimeToUTC(strTime: strTime)
        btnSave.setTitle("", for: .normal)
        
        if type == .logout {
            APIResponseHandler.updatePunchOutTimings(date: apiDate, time: apiTime, completion: {
                status, msg in
                if status {
                    print("Punch out updates successfully..")
                    self.completionHandler()
                    self.removeFromSuperview()
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
        else if type == .login {
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
                        self.completionHandler()
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
        else {
            APIResponseHandler.updateTaskTimings(taskId: self.taskId, date: apiDate, time: apiTime
                , completion: {
                status, msg in
                if status {
                    print("Punch out updates successfully..")
                    
                    // Update end time in core data.
                    let taskTimeCDTrlr = TasksTimeCDController()
                    let strTime = date.getStrTime()
                    taskTimeCDTrlr.updateDateTimings(timeId: self.timeId, date: strTime, startTime: getSecondCountFormat2(strTime: self.startTime), endTime: getTimeInSec()
                        , descr: "")

                    self.completionHandler()
                    self.removeFromSuperview()
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
    }
}
