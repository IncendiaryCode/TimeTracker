//
//  TaskController.swift
//  Attendance Manager
//
//  Created by Sachin on 9/19/19.
//  Copyright © 2019 Sachin. All rights reserved.
//

import Foundation
import UIKit

class TaskController: UIViewController, DateTimePickerDelegate,  UIPickerViewDelegate,
    UIPickerViewDataSource, UITextViewDelegate, UITextFieldDelegate {
    @IBOutlet weak var btnBack: UIButton!
    @IBOutlet weak var btnSave: UIButton!
    @IBOutlet weak var lblAddTask: UILabel!
    @IBOutlet weak var viewAdder: UIScrollView!
    @IBOutlet weak var txtVTaskDescr: UITextView!
    @IBOutlet weak var lblTaskStartTime: UILabel!
    @IBOutlet weak var lblTaskEndTime: UILabel!
    @IBOutlet weak var txtTaskName: UITextField!
    @IBOutlet weak var nsLCViewHider: NSLayoutConstraint!
    @IBOutlet weak var pickerProjectNames: UIPickerView!
    @IBOutlet weak var btnStart: UIButton!
    weak var dateTimePicker: DateTimePicker!
    
    var nSelectedDateLbl = 0
    var cgFStartLblMoveToTop: CGFloat!
    var cgFEndLblMoveToTop: CGFloat!
    var bIsTableVisible = false
    var strSelectedProject: String!
    var taskId: Int?
    var arreProj: Array<String>!
    var taskUpdater: TaskUpdater!
    var projectUpdater: AddProjects!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.view.addGradient(startColor: cgCForGradientStart, endColor: cgCForGradientEnd,
                              cgPStart: CGPoint(x: 0, y: 0), cgPEnd: CGPoint(x: 1, y: 0.3))
        btnStart.addGradient(startColor: cgCForGradientStart, endColor: cgCForGradientEnd,
                             cgFRadius: 20)
        btnStart.drawPlay(layerHeight: 18)
        txtVTaskDescr.layer.borderColor = UIColor.lightGray.cgColor
        lblTaskStartTime.layer.borderColor = UIColor.lightGray.cgColor
        lblTaskEndTime.layer.borderColor = UIColor.lightGray.cgColor
        txtVTaskDescr.delegate = self
        pickerProjectNames.delegate = self
        pickerProjectNames.dataSource = self
        txtTaskName.delegate = self
        
        var tap = UITapGestureRecognizer(target: self, action: #selector(self
            .selectDateAndTimeForStart(_:)))
        lblTaskStartTime.addGestureRecognizer(tap)
        
        tap = UITapGestureRecognizer(target: self, action: #selector(self
            .selectDateAndTimeForEnd(_:)))
        lblTaskEndTime.addGestureRecognizer(tap)
        
        setUpDateTimePicker()
        cgFStartLblMoveToTop = self.view.bounds.height - self.lblTaskStartTime.frame.maxY
            - self.viewAdder.frame.minY - 5
        cgFEndLblMoveToTop = self.view.bounds.height - self.lblTaskEndTime.frame.maxY
            - self.viewAdder.frame.minY - 5
        projectUpdater = AddProjects()
        taskUpdater = TaskUpdater()
        arreProj = projectUpdater.getAllProjectNames()
        strSelectedProject = arreProj[0]
        
        pickerProjectNames.selectRow(arreProj.count/2, inComponent: 0, animated: false)
        
        let time = Date()
        let formatter = DateFormatter()
        formatter.dateFormat = "dd/MM/YYYY hh:mm aa"
        formatter.string(from: time)
        print(formatter.string(from: time))
        lblTaskStartTime.text = formatter.string(from: time)
        if let id = taskId {
            //If any selected task is loaded.
            let dictValues = taskUpdater.getAllData(taskId: id)
            txtTaskName.text = (dictValues["Task Name"]! as! String)
            txtVTaskDescr.text = (dictValues["Task Descr"] as! String)
            pickerProjectNames.selectRow((dictValues["Project Id"]! as! Int)-1, inComponent: 0,
                                                 animated: true)
            if let strDate = dictValues["Start Date"] {
                let nTime = dictValues["Start Time"] as! Int
                let strTime = getSecondsToHourMinute(seconds: nTime)
                formatter.dateFormat = "HH:mm"
                let date = formatter.date(from: strTime)
                formatter.dateFormat = "h:mm a"
                let strTime12 = formatter.string(from: date!)
                lblTaskStartTime.text = "\(strDate) \(strTime12)"
            }
//            btnSave.isHidden = true
//            pickerProjectNames.isUserInteractionEnabled = false
            lblTaskStartTime.isUserInteractionEnabled = false
            strSelectedProject = projectUpdater.getProjectName(projId: dictValues["Project Id"]!
                as! Int)
//            txtTaskName.isUserInteractionEnabled = false
//            txtVTaskDescr.isUserInteractionEnabled = false
        }
    }
    
    override func viewDidAppear(_ animated: Bool) {
        super.viewDidAppear(true)
    }

    func setUpDateTimePicker() {
        //Setup for Date Picker View.
        let min = Date().addingTimeInterval(-60 * 60 * 24 * 4)
        let max = Date().addingTimeInterval(60 * 60 * 24 * 4)
        dateTimePicker = DateTimePicker.create(minimumDate: min, maximumDate: max)
        print(self.view.frame)
        dateTimePicker.frame = CGRect(x: 0, y: self.view.frame.height, width:
            dateTimePicker.frame.size.width, height: dateTimePicker.frame.size.height)
        self.view.addSubview(dateTimePicker)
        
        let btnDone = dateTimePicker.viewWithTag(1) as! UIButton
        btnDone.addGradient(startColor: cgCForGradientStart, endColor: cgCForGradientEnd)
        
        dateTimePicker.delegate = self
        dateTimePicker.dismissHandler = {
            UIView.animate(withDuration: 0.4){
                self.dateTimePicker.frame.origin = CGPoint(x: 0, y: self.view.frame.height)
                if self.nSelectedDateLbl == 1 {
                    self.nsLCViewHider.constant += self.dateTimePicker.bounds.height -
                        self.cgFStartLblMoveToTop + 15
                }
                else if self.nSelectedDateLbl == 2 {
                    self.nsLCViewHider.constant += self.dateTimePicker.bounds.height -
                        self.cgFEndLblMoveToTop + 15
                }
                self.view.layoutIfNeeded()
                self.nSelectedDateLbl = 0
            }
        }
        dateTimePicker.completionHandler = {
            date in
            let formatter = DateFormatter()
            formatter.dateFormat = "dd/MM/YYYY hh:mm aa"
            if self.nSelectedDateLbl == 1 {
                self.lblTaskStartTime.text = formatter.string(from: date)
            }
            else if self.nSelectedDateLbl == 2 {
                self.lblTaskEndTime.text = formatter.string(from: date)
            }
        }
    }
    
    func pickerView(_ pickerView: UIPickerView, numberOfRowsInComponent component: Int) -> Int {
        return arreProj.count
    }
    
    func pickerView(_ pickerView: UIPickerView, viewForRow row: Int, forComponent component: Int,
                    reusing view: UIView?) -> UIView {
        var label: UILabel
        if let view = view as? UILabel { label = view }
        else { label = UILabel() }
        label.text = arreProj[row]
        label.textAlignment = .left
        label.font = UIFont.systemFont(ofSize: 15)
        label.adjustsFontSizeToFitWidth = true
        label.minimumScaleFactor = 0.5
        label.alpha = 0.7
        return label
    }
    
    func pickerView(_ pickerView: UIPickerView, didSelectRow row: Int, inComponent component: Int) {
        strSelectedProject = arreProj[row]
    }
    
    func numberOfComponents(in pickerView: UIPickerView) -> Int {
        return 1
    }
    
    func textField(_ textField: UITextField, shouldChangeCharactersIn range: NSRange,
                   replacementString string: String) -> Bool {
        let currentCharacterCount = textField.text?.count ?? 0
        if range.length + range.location > currentCharacterCount {
            return false
        }
        let newLength = currentCharacterCount + string.count - range.length
        return newLength <= 50
    }
    
    @objc func selectDateAndTimeForStart(_ sender: UITapGestureRecognizer) {
        //Start time selection tapped.
        if nSelectedDateLbl == 0 {
            UIView.animate(withDuration: 0.4){
                self.dateTimePicker.frame.origin = CGPoint(x: 0, y: self.view.frame.height
                    - self.dateTimePicker.bounds.height)
                self.nsLCViewHider.constant -= self.dateTimePicker.bounds.height -
                    self.cgFStartLblMoveToTop + 15
                self.viewAdder.layoutIfNeeded()
            }
            nSelectedDateLbl = 1
        }
    }
    
    @objc func selectDateAndTimeForEnd(_ sender: UITapGestureRecognizer) {
        //End time selection tapped.
        if nSelectedDateLbl == 0 {
            UIView.animate(withDuration: 0.4){
                self.dateTimePicker.frame.origin = CGPoint(x: 0, y: self.view.frame.height
                    - self.dateTimePicker.bounds.height)
                self.nsLCViewHider.constant -= self.dateTimePicker.bounds.height
                    - self.cgFEndLblMoveToTop + 15
                self.viewAdder.layoutIfNeeded()
            }
            nSelectedDateLbl = 2
        }
    }
    
    func dateTimePicker(_ picker: DateTimePicker, didSelectDate: Date) {
        let formatter = DateFormatter()
        formatter.dateFormat = "dd/MM/YYYY hh:mm aa "
        if self.nSelectedDateLbl == 1 {
            self.lblTaskStartTime.text = formatter.string(from: picker.selectedDate)
        }
        else if self.nSelectedDateLbl == 2 {
            self.lblTaskEndTime.text = formatter.string(from: picker.selectedDate)
        }
    }
    
    @IBAction func btnBackPressed(_ sender: Any) {
        self.presentingViewController?.dismiss(animated: true, completion: {self.view = nil})
    }
    
    @IBAction func btnSavePressed(_ sender: Any) {
        guard !(txtTaskName.text == "") else {
            return
        }
        let projAdder = AddProjects()
        let projId = projAdder.getProjectId(project: strSelectedProject)
        if taskId == nil {
            taskUpdater.addNewTask(projectId: projId, taskName: txtTaskName.text!,
                               taskDesc: txtVTaskDescr.text)
        }
        else {
            taskUpdater.updateTaskNameDescrAndProject(taskId: taskId!,
                strTaskName: txtTaskName.text!, strDescr: txtVTaskDescr.text, projectId: projId)
        }
        txtVTaskDescr.text = ""
        txtTaskName.text = ""
        
        taskUpdater.fetchAllData()
        if let viewActivity = self.presentingViewController as? ActivityController {
            viewActivity.sortAndRefreshData()
        }
        self.presentingViewController?.dismiss(animated: true, completion: {self.view = nil})
    }
    
    @IBAction func btnStartPressed(_ sender: Any) {
        let projAdder = AddProjects()
        let projId = projAdder.getProjectId(project: strSelectedProject)
        if taskId == nil {
            guard !(txtTaskName.text == "") else {
                return
            }
            taskUpdater.addNewTask(projectId: projId, taskName: txtTaskName.text!,
                                   taskDesc: txtVTaskDescr.text)
            taskUpdater.fetchAllData()
            taskId = taskUpdater.getPreviouslyAddedTaskId()
        }
        else {
            taskUpdater.updateTaskNameDescrAndProject(taskId: taskId!,
            strTaskName: txtTaskName.text!, strDescr: txtVTaskDescr.text, projectId: projId)
        }
        taskUpdater.startTask(taskId: taskId!)
        txtVTaskDescr.text = ""
        txtTaskName.text = ""
        if let viewActivity = self.presentingViewController as? ActivityController {
            viewActivity.sortAndRefreshData()
        }
        self.presentingViewController?.dismiss(animated: true, completion: {self.view = nil})
    }
    
    @IBAction func txttaskNamePrimaryAction(_ sender: Any) {
        txtVTaskDescr.becomeFirstResponder()
    }
    
    func textView(_ textView: UITextView, shouldChangeTextIn range: NSRange, replacementText text: String) -> Bool {
        if (text == "\n") {
            textView.resignFirstResponder()
        }
        return true
    }
    
    
    @IBAction func viewPressed(_ sender: Any) {
        self.view.endEditing(true)
    }
    
    deinit {
        print("TaskController deinitialized")
    }
}
