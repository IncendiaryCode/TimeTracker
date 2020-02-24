/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : TaskViewController.swift
 //
 //    File Created      : 19:Sept:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : Add/Edit task view controller.
 //
 //////////////////////////////////////////////////////////////////////////// */

import Foundation
import UIKit

class TaskViewController: UIViewController, UITextViewDelegate,
    UITextFieldDelegate, UITableViewDelegate, UITableViewDataSource, DateTimeCellDelegate,
UIGestureRecognizerDelegate {
    
    @IBOutlet weak var btnBack: UIButton!
    @IBOutlet weak var btnSave: UIButton!
    @IBOutlet weak var lblAddTask: UILabel!
    @IBOutlet weak var viewMain: UIView!
    @IBOutlet weak var txtVTaskDescr: UITextView!
    @IBOutlet weak var txtTaskName: UITextField!
    @IBOutlet weak var btnStart: UIButton!
    @IBOutlet weak var tblviewTimings: UITableView!
    @IBOutlet weak var dateTimePicker: UIDatePicker!
    @IBOutlet weak var nsLDatePickerHeight: NSLayoutConstraint!
    @IBOutlet weak var tblForProjAndMod: UITableView!
    @IBOutlet weak var nsLTblProjAndModHeight: NSLayoutConstraint!
    @IBOutlet weak var btnDatePickDone: UIButton!
//    @IBOutlet weak var btnDatePickCancel: UIButton!
    @IBOutlet weak var lblSelectProject: UILabel!
    @IBOutlet weak var lblSelectModule: UILabel!
    @IBOutlet weak var actIndicator: UIActivityIndicatorView!
    @IBOutlet weak var lblError: UILabel!
    @IBOutlet weak var imgDropdownProj: UIImageView!
    @IBOutlet weak var imgDropdownMod: UIImageView!
    @IBOutlet weak var viewBG: UIView!
    @IBOutlet weak var scrollView: UIScrollView!
    @IBOutlet weak var imgHintDate: UIImageView!
    @IBOutlet weak var lblHintDate: UILabel!
    @IBOutlet weak var nsLTblTimingsHeight: NSLayoutConstraint!
    @IBOutlet weak var viewdateHeader: UIView!
    
    /// Selection type .
    enum SelectionType {
        case project
        case module
    }
    
    enum TimeError {
        case startGreater
        case futureTime
        case timeExist
        case undefined
        case updatable
        case punchedout
        case punchin
        case outOfWorkTime
    }
    
    var bIsQuickAction = false
    /// Stores all task timings.
    var arrTaskTimeDetails: Array<TaskTimeDetails>!
    /// Array of prohect details.
    var arrProjects: Array<ProjectDetails>!
    /// Array of deleted task timings id.
    var arrDeletaedTimeId: Array<String>!
    var nSelectedDateLbl = 0
    var cgFStartLblMoveToTop: CGFloat!
    var cgFEndLblMoveToTop: CGFloat!
    var bIsTableVisible = false
    var selectedProjId: Int!
    var selectedModId: Int!
    /// Table view timings cell size.
    var cellHeight: CGFloat = 80
    
    var taskId: Int?
    var arrModules: Array<String>!
    var tasksCDController: TasksCDController!
    var bStateInPlay: Bool?
    var selectedCell: TimingsCell! // Selected cell (Initialise only in delagate from Timings cell).
    /// Classify which row selected. (Date=0, Start Time=1 or End Time=2)
    var selectedRow: Int!
    var nEmptyRow: Int!
    var selectedType: SelectionType!
    /// tableview, Date Picker Height.
    var cgFHeightForPopup: CGFloat!
    /// While adding new timings, timeId should incremented by one.
    var timeId: Int!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        // Update project details.
        APIResponseHandler.loadProjects(completion: { _ in })
        
        // Adding gradient.
        view.addGradient(cgPStart: CGPoint(x: 0, y: 0), cgPEnd: CGPoint(x: 1, y: 0.3))
        btnStart.addGradient(cgFRadius: 22)
        
        // Add observer to get height of keyboard.
        NotificationCenter.default.addObserver(self, selector: #selector(keyboardWillShow), name: UIResponder.keyboardWillShowNotification, object: nil)
        NotificationCenter.default.addObserver(self, selector: #selector(keyboardWillHide),name:
            UIResponder.keyboardWillHideNotification, object: nil)
        
        // Draw play icon.
        btnStart.drawPlay(layerHeight: 18)
        
        // Picker height to zero initially.
        nsLDatePickerHeight.constant = 0
        
        // Setting navigation swipe recognizer.
        self.navigationController?.interactivePopGestureRecognizer?.delegate = nil
        self.navigationController?.interactivePopGestureRecognizer?.isEnabled = true
        
        // Height for pop up table view and date picker.
        cgFHeightForPopup = UIScreen.main.bounds.height * 0.5
        
        tblviewTimings.backgroundColor = g_colorMode.defaultColor()
        tblForProjAndMod.backgroundColor = g_colorMode.defaultColor()
        dateTimePicker.backgroundColor = g_colorMode.defaultColor()
        txtTaskName.useUnderline(uiColor: UIColor.lightGray.withAlphaComponent(0.3))
        txtVTaskDescr.useUnderline(uiColor: UIColor.lightGray.withAlphaComponent(0.3))
        txtVTaskDescr.layer.borderColor = UIColor.clear.cgColor
        lblSelectProject.useUnderline(uiColor: UIColor.lightGray.withAlphaComponent(0.3))
        lblSelectModule.useUnderline(uiColor: UIColor.lightGray.withAlphaComponent(0.3))
        tblForProjAndMod.layer.borderColor = UIColor.lightGray.cgColor
        tblviewTimings.tableHeaderView?.backgroundColor = .clear
        btnDatePickDone.setTitleColor(.white, for: .normal)
        let cgPStart = CGPoint(x: 0, y: 0.25)
        let cgPEnd = CGPoint(x: 1, y: 0.75)
        btnDatePickDone.addGradient(cgPStart: cgPStart, cgPEnd: cgPEnd, cgFRadius: 15)
        txtVTaskDescr.textColor = g_colorMode.midText()
        txtTaskName.textColor = g_colorMode.textColor()
        viewMain.backgroundColor = g_colorMode.defaultColor()
        scrollView.layer.masksToBounds = true
        scrollView.layer.cornerRadius = 35
        scrollView.backgroundColor = g_colorMode.defaultColor()
        scrollView.layer.borderColor = g_colorMode.lineColor().cgColor
        scrollView.layer.borderWidth = 0.3
        
        arrTaskTimeDetails = Array<TaskTimeDetails>()
        arrDeletaedTimeId = Array<String>()
        
        dateTimePicker.addTarget(self, action: #selector(datePickerChanged(picker:)),
                                 for: .valueChanged)
        
        tblviewTimings.delegate = self
        tblviewTimings.dataSource = self
        tblviewTimings.register(UINib(nibName: "TimingsCell", bundle: nil), forCellReuseIdentifier:
            "DateTimeCell")
        
        // Add tap gesture to label select project and module.
        var tap = UITapGestureRecognizer(target: self, action: #selector
            (lblSelectProjectPressed))
        lblSelectProject.isUserInteractionEnabled = true
        lblSelectProject.addGestureRecognizer(tap)
        tap = UITapGestureRecognizer(target: self, action: #selector
            (lblSelectModulePressed))
        lblSelectModule.isUserInteractionEnabled = true
        lblSelectModule.addGestureRecognizer(tap)
        
        // Tap gesture to BG view.
        tap = UITapGestureRecognizer(target: self, action: #selector
            (viewBGPressed(sender:)))
        viewBG.addGestureRecognizer(tap)
        
        // Tap gesture to Scroll view.
        tap = UITapGestureRecognizer(target: self, action: #selector
            (scrollViewPressed(sender:)))
        tap.delegate = self
        scrollView.canCancelContentTouches = false
        scrollView.delegate = self
        scrollView.addGestureRecognizer(tap)
        viewBG.backgroundColor = UIColor.black.withAlphaComponent(0.7)
        
        var panGesture = UIPanGestureRecognizer(target: self, action:#selector(self
            .panToTableview(panGesture:)))
        tblForProjAndMod.addGestureRecognizer(panGesture)
        
        panGesture = UIPanGestureRecognizer(target: self, action:#selector(self
            .panToDatePickerTop(panGesture:)))
        viewdateHeader.addGestureRecognizer(panGesture)

        txtVTaskDescr.delegate = self
        txtTaskName.delegate = self
        
        tasksCDController = TasksCDController()
        // Map dictionary values to array.
        arrProjects = g_dictProjectDetails.map { $0.1 }
        
        tblForProjAndMod.delegate = self
        tblForProjAndMod.dataSource = self
        
        // Set initial cell count if add task segue.
        nEmptyRow = 1
        
        btnStart.isHidden = true // May change in future.
        
        if let id = taskId {
            // If any selected task is loaded.
            // Get task information.
            let cTaskDetails = getTaskDetails(taskId: id)!
            txtTaskName.text = cTaskDetails.taskName
            if nil != cTaskDetails.taskDescription || "" != cTaskDetails.taskDescription {
                txtVTaskDescr.text = cTaskDetails.taskDescription
                txtVTaskDescr.textColor = g_colorMode.textColor()
            }
            lblSelectProject.textColor = g_colorMode.textColor()
            lblSelectModule.textColor = g_colorMode.textColor()
            selectedProjId = cTaskDetails.projId
            arrModules = g_dictProjectDetails[selectedProjId]?.dictModules.map { $0.1 }
            lblSelectProject.text = getProjectName(projId: selectedProjId)
            selectedModId = cTaskDetails.moduleId
            let cProjDetails = g_dictProjectDetails[selectedProjId]!
            lblSelectModule.text = cProjDetails.dictModules[selectedModId]
            
            // Get task timings.
            arrTaskTimeDetails = cTaskDetails.arrTaskTimings
            sortAndDisplayTimings()
            lblAddTask.text = "Edit Task"
            
            // Set empty cells to zero.
            nEmptyRow = 0
        }
    }
    
    override func viewDidDisappear(_ animated: Bool) {
        super.viewWillDisappear(animated)
        let center = NotificationCenter.default
        center.removeObserver(self, name: UIResponder.keyboardDidShowNotification, object: nil)
        center.removeObserver(self, name: UIResponder.keyboardWillHideNotification, object: nil)
    }
    
    /// Updates tableview data.
    func sortAndDisplayTimings() {
        sortArrayTimings()
        tblviewTimings.reloadData()
    }
    
    // Sort timings date and time.
    func sortArrayTimings() {
        // Sort array timings.
        if arrTaskTimeDetails.count > 0 {
            let sortedArrayTime = arrTaskTimeDetails.sorted(by: {
                ($0.nStartTime) < ($1.nStartTime)
            })
            let sortedArrayDate = sortedArrayTime.sorted (by: {
                getDateFromString(strDate: ($0.strDate)) < getDateFromString(strDate:
                    ($1.strDate))
            })
            arrTaskTimeDetails = sortedArrayDate
        }
    }
    
    @objc func lblSelectProjectPressed(sender: UITextField) -> Void {
        lblError.isHidden = true // If error label shown hide it.
        selectedType = .project
        tblForProjAndMod.reloadData()
        imgDropdownProj.isHidden = true //Hide dropdown icon.
        showTableProjectModule()
        // If keyboard displayed end editing.
        self.view.endEditing(true)
    }
    
    func showTableProjectModule() {
        viewBG.isHidden = false
        nsLTblProjAndModHeight.constant = cgFHeightForPopup
        UIView.animate(withDuration: g_nAnimatnDuratn) {
            self.view.layoutIfNeeded()
        }
    }
    
    /// To handle pan gesture to tableview project and module selector.
    @objc func panToTableview(panGesture: UIPanGestureRecognizer) {
        let translation = panGesture.translation(in: self.view)
        if tblForProjAndMod.frame.minY + translation.y >= cgFHeightForPopup {
            // Transition.
            nsLTblProjAndModHeight.constant = cgFHeightForPopup - translation.y
            let progress = (nsLTblProjAndModHeight.constant) / (cgFHeightForPopup)
            viewBG.alpha = progress
        }
        if panGesture.state == .ended || panGesture.state == .cancelled ||
            panGesture.state == .failed {
            // If table moved below to more than half of its height.
            if nsLTblProjAndModHeight.constant < cgFHeightForPopup/2 || panGesture.velocity(in: viewMain).y > 500 {
                // Dismiss view.
                nsLTblProjAndModHeight.constant = 0
                UIView.animate(withDuration: g_nAnimatnDuratn+0.1, animations: {
                    self.view.layoutIfNeeded()
                }) { _ in
                    self.viewBG.alpha = 1
                    self.viewBG.isHidden = true
                    self.imgDropdownProj.isHidden = false
                    self.imgDropdownMod.isHidden = false
                }
            }
            else {
                // Move to origin.
                nsLTblProjAndModHeight.constant = cgFHeightForPopup
                UIView.animate(withDuration: g_nAnimatnDuratn+0.1, animations: {
                    self.view.layoutIfNeeded()
                    self.viewBG.alpha = 1
                    self.imgDropdownProj.isHidden = false
                    self.imgDropdownMod.isHidden = false
                })
            }
        }
    }
    
    /// To handle pan gesture to date picker.
    @objc func panToDatePickerTop(panGesture: UIPanGestureRecognizer) {
        let translation = panGesture.translation(in: self.view)
        if dateTimePicker.frame.minY + translation.y >= cgFHeightForPopup {
            // Translation.
            nsLDatePickerHeight.constant = cgFHeightForPopup - translation.y
            let progress = (nsLDatePickerHeight.constant) / (cgFHeightForPopup)
            viewBG.alpha = progress
        }
        if panGesture.state == .ended || panGesture.state == .cancelled ||
            panGesture.state == .failed {
            // If table moved below to more than half of its height.
            if nsLDatePickerHeight.constant < cgFHeightForPopup/2 || panGesture.velocity(in: viewMain).y > 500 {
                // Dismiss view.
                nsLDatePickerHeight.constant = 0
                UIView.animate(withDuration: g_nAnimatnDuratn+0.1, animations: {
                    self.view.layoutIfNeeded()
                }) { _ in
                    self.viewBG.alpha = 1
                    self.viewBG.isHidden = true
                }
            }
            else {
                // Move to origin.
                nsLDatePickerHeight.constant = cgFHeightForPopup
                UIView.animate(withDuration: g_nAnimatnDuratn+0.1, animations: {
                    self.view.layoutIfNeeded()
                    self.viewBG.alpha = 1
                })
            }
        }
    }
    
    /// To hide table view(Projects and module).
    func hideTableProjectModule() {
        viewBG.isHidden = true
        nsLTblProjAndModHeight.constant = 0
        UIView.animate(withDuration: g_nAnimatnDuratn+0.1) {
            self.view.layoutIfNeeded()
        }
        //Show dropdown icon.
        imgDropdownProj.isHidden = false
        imgDropdownMod.isHidden = false
    }
    
    @objc func lblSelectModulePressed(sender:UITextField) -> Void {
        // If before selection of project, module selected
        guard nil != selectedProjId else {
            lblError.text = "Please select project"
            lblError.isHidden = false
            scrollView.scrollToTop()
            return
        }
        selectedType = .module
        tblForProjAndMod.reloadData()
        imgDropdownMod.isHidden = true //Hide dropdown icon.
        showTableProjectModule()
        // If keyboard displayed end editing.
        self.view.endEditing(true)
    }
    
    func gestureRecognizer(_ gestureRecognizer: UIGestureRecognizer, shouldReceive
        touch: UITouch) -> Bool {
        /// Touch gesture only to view and mainview.
        if touch.view == self.viewBG || touch.view == self.viewMain || touch.view == scrollView {
            return true
        }
        return false
    }
    
    func tableView(_ tableView: UITableView, viewForHeaderInSection section: Int) -> UIView? {
        if tableView == tblviewTimings {
            let lblHeader = UILabel()
            lblHeader.text = "                       Date                 Start               End"
            lblHeader.font = lblHeader.font.withSize(12)
            lblHeader.textColor = .lightGray
            return lblHeader
        }
        else {
            let header = TableHeaderView()
            switch selectedType {
                case .project: header.customInit(title: "Choose project", section: section)
                case .module: header.customInit(title: "Choose module", section: section)
                case .none:
                    break
            }
            header.lblTitle.center = CGPoint(x: header.lblTitle.frame.midX, y: 20)
            header.contentView.backgroundColor = g_colorMode.defaultColor()
            header.btnFilter.isHidden = true
            return header
        }
    }
    
    func tableView(_ tableView: UITableView, heightForHeaderInSection section: Int) -> CGFloat {
        if tableView == tblviewTimings {
            return 30
        }
        else {
            return 40
        }
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        if tableView == tblviewTimings {
            // Set timings height.
            nsLTblTimingsHeight.constant = (CGFloat(arrTaskTimeDetails.count)*cellHeight)+(CGFloat(nEmptyRow)*cellHeight)+44.0+30.0+20 // (44 - add time label, 30 - header, 20 - Hide scroll view border rad)
            return arrTaskTimeDetails.count + nEmptyRow + 1 // +1 is for add time label.
        }
        else {
            switch selectedType {
                case .project:
                    return arrProjects.count
                case .module:
                    if nil != arrModules {
                        return arrModules.count
                }
                    else {
                        return 0
                }
                default:
                    return 0
            }
        }
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        if tableView == tblviewTimings {
            let cell = tableView.dequeueReusableCell(withIdentifier: "DateTimeCell", for: indexPath)
                as! TimingsCell
            cell.delegate = self
            cell.indexPath = indexPath
            cell.contentView.backgroundColor = g_colorMode.defaultColor()
            if indexPath.row < arrTaskTimeDetails.count {
                // If edit field cell.
                let cTaskTimeDetails = arrTaskTimeDetails[indexPath.row]
                cell.timeId = cTaskTimeDetails.timeId
                cell.txtFDescription.text = cTaskTimeDetails.description
                cell.lblDate.text = cTaskTimeDetails.strDate
                // If indexpath row not equals 0 and previos cell date is same the hide date label.
                if 0 != indexPath.row && cTaskTimeDetails.strDate ==
                arrTaskTimeDetails[indexPath.row-1].strDate {
                    cell.lblDate.isHidden = true
                    cell.viewSeparator.isHidden = true
                }
                else {
                    cell.lblDate.isHidden = false
                    cell.lblDate.text = cTaskTimeDetails.strDate
                    cell.viewSeparator.isHidden = false
                }
                let nStartTime = cTaskTimeDetails.nStartTime!
                let nEndTime = cTaskTimeDetails.nEndTime!
                let descr = cTaskTimeDetails.description
                
                let strStartTime = getSecondsToHourMinute(seconds: nStartTime)
                let strEndTime = getSecondsToHourMinute(seconds: nEndTime)
                cell.lblStartTime.text = "\(convert24to12FormatHourMinute(strTime: strStartTime))"
                cell.lblEndTime.text = "\(convert24to12FormatHourMinute(strTime: strEndTime))"
                cell.txtFDescription.text = descr
                cell.btnRemoveAdd.setImage(#imageLiteral(resourceName: "remove icon"), for: .normal)
                cell.btnRemoveAdd.setTitle("Remove", for: .normal)
                cell.btnRemoveAdd.setTitleColor(.clear, for: .normal)
                
                // add right border line
                cell.lblStartTime.layer.masksToBounds = false
                cell.lblDate.layer.masksToBounds = false
                cell.lblStartTime.isHidden = false
                cell.lblEndTime.isHidden = false
                cell.txtFDescription.isHidden = false
                
            }
            // When timings cell added but, timings not set.
            else if nEmptyRow == 1 && indexPath.row != arrTaskTimeDetails.count + nEmptyRow {
                setupRemoveEmptyButton(cell: cell)
                // If first cell.
                if indexPath.row == 0 {
                    // First entry prefill.
//                    cell.btnRemoveAdd.isHidden = true
                    cell.lblDate.text = Date().getStrDate()
                    cell.lblStartTime.text = Date().getStrTime()
                }
            }
            else {
                // If add time cell.
                cell.lblDate.isUserInteractionEnabled = true
                cell.lblDate.isHidden = false
                cell.lblDate.text = "Add Timeline"
                cell.viewSeparator.isHidden = false
                cell.lblStartTime.isHidden = true
                cell.lblEndTime.isHidden = true
                cell.txtFDescription.isHidden = true
                
                // remove right border line
                cell.lblStartTime.layer.masksToBounds = true
                cell.lblDate.layer.masksToBounds = true
                
                cell.btnRemoveAdd.setImage(#imageLiteral(resourceName: "plus_icon"), for: .normal)
                cell.btnRemoveAdd.setTitle("Add", for: .normal)
                cell.btnRemoveAdd.setTitleColor(.clear, for: .normal)
            }
            return cell
        }
        else {
            let cell = tableView.dequeueReusableCell(withIdentifier: "ProjectModuleCell")!
            let lblTitle = cell.viewWithTag(1) as! UILabel
            let imgProj = cell.viewWithTag(2) as! UIImageView
            let imgSelected = cell.viewWithTag(3) as! UIImageView
            imgSelected.image = #imageLiteral(resourceName: "rightIcon")
            imgSelected.backgroundColor = g_colorMode.midColor()
            // set hidden if nothing selected.
            imgSelected.isHidden = true
            
            // Indicates initial load of cell.
            if lblTitle.text == "" {
                let cgRect = CGRect(x: imgProj.frame.minX, y: imgProj.frame.maxY+10, width:
                    self.view.frame.maxX, height: 1)
                // If first time load only add line.
                cell.contentView.addLine(rect: cgRect)
            }
            switch selectedType {
                case .project :
                    lblTitle.text = arrProjects[indexPath.row].projName
                    imgProj.image = arrProjects[indexPath.row].imgProjIcon
                    if nil != selectedProjId && arrProjects[indexPath.row].projId == selectedProjId {
                        imgSelected.isHidden = false
                    }
                case .module:
                    lblTitle.text = arrModules[indexPath.row]
                    imgProj.image = nil
                    let modId = g_dictProjectDetails[selectedProjId!]!
                        .dictModules.getKey(forValue: arrModules[indexPath.row])
                    if nil != selectedModId && modId == selectedModId {
                        imgSelected.isHidden = false
                    }
                default:
                break
            }
            return cell
        }
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        if tableView == tblviewTimings {

        }
        else {
            switch selectedType {
                case .project:
                    let strProject = arrProjects[indexPath.row].projName
                    selectedProjId = arrProjects[indexPath.row].projId
                    arrModules = arrProjects[indexPath.row].dictModules.map { $0.1 }
                    lblSelectProject.text = strProject
                    // Set module to nil.
                    selectedModId = nil
                    lblSelectModule.text = "Select module"
                case .module:
                    let strModule = arrModules[indexPath.row]
                    selectedModId = g_dictProjectDetails[selectedProjId!]!
                        .dictModules.getKey(forValue: strModule)
                    lblSelectModule.text = strModule
                default:
                break
            }
            
            // Change font color of label project and module.
            if lblSelectProject.text == "Select project" {
                lblSelectProject.textColor = g_colorMode.midText()
                
            }
            else {
                lblSelectProject.textColor = g_colorMode.textColor()
            }
            if lblSelectModule.text == "Select module" {
                lblSelectModule.textColor = g_colorMode.midText()
            }
            else {
                lblSelectModule.textColor = g_colorMode.textColor()
            }
            
            hideTableProjectModule()
        }
    }
    
    /// When date time picker value changed called.
    @objc func datePickerChanged(picker: UIDatePicker) {
//        let date = picker.date
//        if selectedRow == 0 {
//            let strDate = date.getStrDate()
//            selectedCell.lblDate.text = strDate
//        }
//        else if selectedRow == 1 {
//            let strStartTime = date.getStrTime()
//            selectedCell.lblStartTime.text = strStartTime
//        }
//        else {
//            let strStartTime = date.getStrTime()
//            selectedCell.lblEndTime.text = strStartTime
//        }
    }
    
    func removeAddSelected(indexPath: IndexPath) {
        lblError.isHidden = true
        // Remove selected task time
        let cell = tblviewTimings.cellForRow(at: indexPath) as! TimingsCell
        selectedCell = cell
        if cell.btnRemoveAdd.titleLabel!.text == "Remove" {
            
            cell.nsLLeadeingSpace.constant = -100
            UIView.animate(withDuration: 0.3, animations: {
                self.tblviewTimings.layoutIfNeeded()
            })
            
            //Desable touch to selected cell while remove action performed.
//            cell.lblDate.isUserInteractionEnabled = false
//            cell.lblStartTime.isUserInteractionEnabled = false
//            cell.lblEndTime.isUserInteractionEnabled = false
        }
        // If selected add time.
        else if nEmptyRow == 0 {
            let index = IndexPath(row: arrTaskTimeDetails.count+1, section: 0)
            nEmptyRow = 1
            setupRemoveEmptyButton(cell: cell)
            CATransaction.begin()
            tblviewTimings.insertRows(at: [index], with: .fade)
            CATransaction.setCompletionBlock({self.scrollView.scrollToBottom()})
            CATransaction.commit()
        }
        else {
            errorMessage(msg: "Please complete previous time")
        }
    }
    
    func setupRemoveEmptyButton(cell: TimingsCell) {
        cell.btnRemoveAdd.setTitle("Remove", for: .normal)
        cell.btnRemoveAdd.setImage(#imageLiteral(resourceName: "remove icon"), for: .normal)
        cell.btnRemoveAdd.setTitleColor(.clear, for: .normal)
        
        // add right border line
        cell.lblStartTime.layer.masksToBounds = false
        cell.lblDate.layer.masksToBounds = false
        cell.lblDate.text = ""
        cell.lblStartTime.text = ""
        cell.lblEndTime.text = ""
        cell.txtFDescription.text = ""
        cell.lblDate.isHidden = false
        cell.viewSeparator.isHidden = false
        cell.lblStartTime.isHidden = false
        cell.lblEndTime.isHidden = false
        cell.txtFDescription.isHidden = false
    }
    
    /// Remove confirmed, delete value from array and set up view.
    func removeConfirmSelected(indexPath: IndexPath) {
        let cell = tblviewTimings.cellForRow(at: indexPath) as! TimingsCell
        selectedCell = cell
        removeArrayTimings()
        setCellToConstantPosition()
    }
    
    // When cell left swiped and to set it actual position.
    func setCellToConstantPosition() {
        for visibleCell in tblviewTimings.visibleCells {
            let cell = (visibleCell as! TimingsCell)
            cell.nsLLeadeingSpace.constant = 0
            UIView.animate(withDuration: g_nAnimatnDuratn+0.1, animations: {
                self.tblviewTimings.layoutIfNeeded()
            })
            
            // Enable touch on cell.
            cell.lblDate.isUserInteractionEnabled = true
            cell.lblStartTime.isUserInteractionEnabled = true
            cell.lblEndTime.isUserInteractionEnabled = true
        }
    }
    
    func addNewCellTimings() {
        if .updatable == checkTimeUpdatable().0 {
            addArrayTimings()
            sortAndDisplayTimings()
        }
    }
    
    /// Validates time. (Whether updation possible or not).
    func checkTimeUpdatable() -> (TimeError, String?, String?) {
        guard nil != selectedCell && !selectedCell.lblDate.text!.isEmpty &&
            !selectedCell.lblStartTime.text!.isEmpty && !selectedCell.lblEndTime.text!.isEmpty else
        {
            return (.undefined, nil, nil)
        }
        
        var start = getSecondCount(strTime: selectedCell.lblStartTime.text!)
        var end = getSecondCount(strTime: selectedCell.lblEndTime.text!)
        let id = selectedCell.timeId
        var date = selectedCell.lblDate.text!
        
        // For validation take picker value.
        let dateInPicker = dateTimePicker.date
        if selectedRow == 0 {
            date = dateInPicker.getStrDate()
        }
        else if selectedRow == 1 {
            start = getSecondCount(strTime: dateInPicker.getStrTime())
        }
        else {
            end = getSecondCount(strTime: dateInPicker.getStrTime())
        }
        
        /// Start time is greater than end time
        if start >= end {
            return (.startGreater, nil, nil)
        }
        
        // Validate time in between working hour.(Punch in/out)
        let startTime = getSecondsToHoursMinutesSeconds(seconds: start)
        let endTime = getSecondsToHoursMinutesSeconds(seconds: end)
        let punchInOutCDCtrlr = PunchInOutCDController()
        let startDate = convertStrDateTimeToDate(strDateTime:
            "\(date) \(startTime)")
        let endDate = convertStrDateTimeToDate(strDateTime:
            "\(date) \(endTime)")
        let (isOutOfrange, strStart, strEnd) = punchInOutCDCtrlr.isTimeExistInPunchInOut(start:
            startDate, end: endDate)
        if !isOutOfrange {
            return (.outOfWorkTime, strStart, strEnd)
        }
        
        
        /// Date and time is greater than current time.
//        if date == Date().getStrDate() {
//            if (start-60) > getTimeInSec() || (end-60) > getTimeInSec() {
//                return .futureTime
//            }
//        }
        
        for i in 0..<arrTaskTimeDetails.count {
            let cTaskTimeDetails = arrTaskTimeDetails[i]
            // Updating selected cell(So, the selected cell not compared).
            if id != cTaskTimeDetails.timeId {
                if date == cTaskTimeDetails.strDate {
                    
                    // Get seconds
                    let nStartSec = cTaskTimeDetails.nStartTime % 60
                    let nEndSec = cTaskTimeDetails.nEndTime % 60
                    
                    // Remove seconds from time
                    let nStartTime = cTaskTimeDetails.nStartTime - nStartSec
                    let nEndTime = cTaskTimeDetails.nEndTime - nEndSec
                    // When start time in between any other start and end time.
                    
                    print("validation \(getSecondsToHoursMinutesSeconds(seconds: start)) \(getSecondsToHoursMinutesSeconds(seconds: end)) \(getSecondsToHoursMinutesSeconds(seconds: nStartTime)) \(getSecondsToHoursMinutesSeconds(seconds: nEndTime))")
                    
                     print("validation \(start) \(end) \t \(nStartTime) \(nEndTime) ")
                    
                    print("validation \(nStartTime) < \(start) \t \(nEndTime) > \(start) ")
                    
                    print("validation \(end) > \(nStartTime) \t \(end) < \(nEndTime) ")
                    
                    print("validation \(start) < \(nStartTime) \t \(end) > \(nEndTime) \n.............\n")
                    if nStartTime <= start && nEndTime > start {
                        return (.timeExist, nil, nil)
                    }
                    // When end time in between any other start and end time.
                    else if end > nStartTime && end < nEndTime {
                        return (.timeExist, nil, nil)
                    }
                    // When start time less than and end time greater than any other timings.
                    else if start < nStartTime && end > nEndTime {
                        return (.timeExist, nil, nil)
                    }
                }
            }
        }
        return (.updatable, nil, nil)
    }
    
    func dateSelected(indexPath: IndexPath) {
        // If cell is swiped left side.
        setCellToConstantPosition()
        
        let cell = tblviewTimings.cellForRow(at: indexPath) as! TimingsCell
        if cell.lblDate.text != "Add Timeline" {
            // Change date selected index.
            dateTimePicker.datePickerMode = .date
            let minDate = NSCalendar.current.date(byAdding: .year, value: -1, to: NSDate() as
                Date)
            let maxDate = Date()
            dateTimePicker.minimumDate = minDate
            dateTimePicker.maximumDate = maxDate
            showDateTimePicker()
            selectedCell = cell
            selectedRow = 0 // Date is 0
            
            if !cell.lblDate.text!.isEmpty {
                // If cell is edit field.
                let dateSelected = getDateFromString(strDate: cell.lblDate.text!)
                dateTimePicker.setDate(dateSelected, animated: true)
            }
            else {
                dateTimePicker.setDate(Date(), animated: true)
//                cell.lblDate.text = Date().getStrDate()
            }
            
//            tblviewTimings.scrollToRow(at: indexPath, at: .bottom, animated: false)
        }
        else {
            // If last index. (i.e. add new add time cell)
            removeAddSelected(indexPath: indexPath)
        }
        // Show hint.
        lblHintDate.isHidden = true
        imgHintDate.isHidden = true
    }
    
    func startTimeSelected(indexPath: IndexPath) {
        // If cell is swiped left side.
        setCellToConstantPosition()
        
        // Change start time selected index.
        let cell = tblviewTimings.cellForRow(at: indexPath) as! TimingsCell
        showDateTimePicker()
        selectedCell = cell
        selectedRow = 1 // Date is 0
        if !cell.lblStartTime.text!.isEmpty && !cell.lblDate.text!.isEmpty {
            let date = "\(cell.lblDate.text!) \(cell.lblStartTime.text!)"
            let timeStart = getDateFromTime(strTime: date)
            setTimePickerView(dateTime: timeStart)
        }
        else {
            setTimePickerView(dateTime: Date())
//            cell.lblStartTime.text = Date().getStrTime()
        }
//        tblviewTimings.scrollToRow(at: indexPath, at: .bottom, animated: false)
        // Hide hint.
        lblHintDate.isHidden = true
        imgHintDate.isHidden = true
    }
    
    func endTimeSelected(indexPath: IndexPath) {
        // If cell is swiped left side.
        setCellToConstantPosition()
        
        // Change end time selected index.
        let cell = tblviewTimings.cellForRow(at: indexPath) as! TimingsCell
        showDateTimePicker()
        selectedCell = cell
        selectedRow = 2 // Date is 0
        if !cell.lblEndTime.text!.isEmpty && !cell.lblDate.text!.isEmpty {
            let dateTime = "\(cell.lblDate.text!) \(cell.lblEndTime.text!)"
            let timeEnd = getDateFromTime(strTime: dateTime)
            setTimePickerView(dateTime: timeEnd)
        }
        else {
            setTimePickerView(dateTime: Date())
//            cell.lblEndTime.text = Date().getStrTime()
        }
//        tblviewTimings.scrollToRow(at: indexPath, at: .bottom, animated: false)
        // Hide hint.
        lblHintDate.isHidden = true
        imgHintDate.isHidden = true
    }
    
    func endEditingDescr(indexPath: IndexPath) {
        let cell = tblviewTimings.cellForRow(at: indexPath) as! TimingsCell
        selectedCell = cell
        selectedRow = 3
        editDateTimeCompleted()
    }
    
    func startEditingDescr(indexPath: IndexPath) {
        // If cell is swiped left side.
        setCellToConstantPosition()
        
        let cell = tblviewTimings.cellForRow(at: indexPath) as! TimingsCell
        selectedCell = cell
        selectedRow = 3
    }
    
    /// Notifier when keyboard shown.
    @objc func keyboardWillShow(notification: NSNotification) {
        if let keyboardHeight = (notification.userInfo?[UIResponder.keyboardFrameEndUserInfoKey] as? NSValue)?.cgRectValue.height {
            self.scrollView.contentInset = UIEdgeInsets(top: 0, left: 0, bottom: keyboardHeight
                , right: 0)
//            nsLTblTimingsHeight.constant -= keyboardHeight
        }
    }
    
    /// otifier when keyboard hidden.
    @objc func keyboardWillHide(notification: NSNotification) {
        if let _ = (notification.userInfo?[UIResponder.keyboardFrameEndUserInfoKey] as? NSValue)?
            .cgRectValue.height {
            self.scrollView.contentInset = UIEdgeInsets(top: 0, left: 0, bottom: 0, right: 0)
//            nsLTblTimingsHeight.constant += keyboardHeight
        }
    }
    
    /// Setup time picker.(Maximum and Minimum time)
    func setTimePickerView(dateTime: Date) {
        dateTimePicker.datePickerMode = .time
//        let calendar = Calendar.current
//        var components = DateComponents()
//        components.day = dateTime.day
//        components.month = dateTime.mon
//        components.year = dateTime.year
//        components.hour = 8 // Minimum time from 6 AM
//        components.minute = 00
//        let minTime = calendar.date(from: components)!
        dateTimePicker.minimumDate = nil
//        components.hour = 20 // // Max time to 8 PM
//        let maxTime = calendar.date(from: components)!
        dateTimePicker.maximumDate = nil
        dateTimePicker.setDate(dateTime, animated: true)
    }
    
    @IBAction func btnDatePickDonePressed(_ sender: Any) {
        if nil != selectedCell {
            let date = dateTimePicker.date
            if 0 == selectedRow {
                let strDate = date.getStrDate()
                selectedCell.lblDate.text = strDate
            }
            else if 1 == selectedRow{
                let strStartTime = date.getStrTime()
                selectedCell.lblStartTime.text = strStartTime
            }
            else if 2 == selectedRow {
                let strEndTime = date.getStrTime()
                selectedCell.lblEndTime.text = strEndTime
            }
            else {
            }
        }
        editDateTimeCompleted()
    }
    
    func editDateTimeCompleted() {
        guard !selectedCell.lblDate.text!.isEmpty && !selectedCell.lblStartTime.text!.isEmpty &&
            !selectedCell.lblEndTime.text!.isEmpty else {
                // If all the fields are not set.
                let date = dateTimePicker.date
                if selectedRow == 0 {
                    let strDate = date.getStrDate()
                    selectedCell.lblDate.text = strDate
                    
                    // If selected time line is from existing times.
                    if let timeId = selectedCell.timeId, timeId > 0 {
                        arrTaskTimeDetails[selectedCell.indexPath.row].strDate = strDate
                    }
                }
                else if selectedRow == 1 {
                    let strStartTime = date.getStrTime()
                    selectedCell.lblStartTime.text = strStartTime
                    
                    // If selected time line is from existing times.
                    if let timeId = selectedCell.timeId, timeId > 0 {
                        let startTime = getSecondCount(strTime: strStartTime)
                        arrTaskTimeDetails[selectedCell.indexPath.row].nStartTime = startTime
                    }
                }
                else if selectedRow == 2 {
                    let strEndTime = date.getStrTime()
                    selectedCell.lblEndTime.text = strEndTime
                    
                    // If selected time line is from existing times.
                    if let timeId = selectedCell.timeId, timeId > 0 {
                        let endTime = getSecondCount(strTime: strEndTime)
                        arrTaskTimeDetails[selectedCell.indexPath.row].nStartTime = endTime
                    }
                }
                else if arrTaskTimeDetails.count > selectedCell.indexPath.row {
                    arrTaskTimeDetails[selectedCell.indexPath.row].description =
                        selectedCell.txtFDescription.text
                }
                hideDateTimePicker()
                return
        }
        let timesCheck = checkTimeUpdatable()
        if .updatable == timesCheck.0 {
            // Update edit timingss.
            let date = dateTimePicker.date
            if selectedRow == 0 {
                let strDate = date.getStrDate()
                selectedCell.lblDate.text = strDate
                
                // If selected time line is from existing times.
                if let timeId = selectedCell.timeId, timeId > 0 {
                    arrTaskTimeDetails[selectedCell.indexPath.row].strDate = strDate
                }
            }
            else if selectedRow == 1 {
                let strStartTime = date.getStrTime()
                selectedCell.lblStartTime.text = strStartTime
                
                // If selected time line is from existing times.
                if let timeId = selectedCell.timeId, timeId > 0 {
                    let startTime = getSecondCount(strTime: strStartTime)
                    arrTaskTimeDetails[selectedCell.indexPath.row].nStartTime = startTime
                }
            }
            else if selectedRow == 2 {
                let strEndTime = date.getStrTime()
                selectedCell.lblEndTime.text = strEndTime
                
                // If selected time line is from existing times.
                if let timeId = selectedCell.timeId, timeId > 0 {
                    let endTime = getSecondCount(strTime: strEndTime)
                    arrTaskTimeDetails[selectedCell.indexPath.row].nEndTime = endTime
                }
            }
            else {
                if let timeId = selectedCell.timeId, timeId > 0 {
                    arrTaskTimeDetails[selectedCell.indexPath.row].description =
                        selectedCell.txtFDescription.text
                }
            }
            // Edit Action.
            if selectedCell.indexPath.row < arrTaskTimeDetails.count {
                // If selected cell is edit time.
                // Update timings in array also.
                updateArrayTimings()
            }
            else {
                // If selected cell type is adding new date.
                addNewCellTimings()
            }
        }
        else {
            alertUnableToChangeDate(type: timesCheck.0, startTime: timesCheck.1
                , endTime: timesCheck.2)
        }
        hideDateTimePicker()
    }
    
    /// Update timings in array.
    func updateArrayTimings() {
        for i in 0..<arrTaskTimeDetails.count {
            let selectedTimeId = selectedCell.timeId
            let timeId = arrTaskTimeDetails[i].timeId
            if selectedTimeId == timeId {
                if selectedRow == 0 {
                    let strDate = selectedCell.lblDate.text!
                    arrTaskTimeDetails[i].strDate = strDate
                }
                else if selectedRow == 1 {
                    let startTime = getSecondCount(strTime: selectedCell.lblStartTime.text!)
                    arrTaskTimeDetails[i].nStartTime = startTime
                }
                else if selectedRow == 2 {
                    let endTime = getSecondCount(strTime: selectedCell.lblEndTime.text!)
                    arrTaskTimeDetails[i].nEndTime = endTime
                }
                else {
                    // Update description.
                    let strDescr = selectedCell.txtFDescription.text
                    arrTaskTimeDetails[i].description = strDescr
                }
                break
            }
        }
        sortArrayTimings()
        tblviewTimings.reloadData()
    }

    /// Update timings in array.
    func removeArrayTimings() {
        var isEmptyCell = true
        for i in 0..<arrTaskTimeDetails.count {
            let selectedTimeId = selectedCell.timeId
            let timeId = arrTaskTimeDetails[i].timeId
            if selectedTimeId == timeId {
                arrTaskTimeDetails.remove(at: i)
                isEmptyCell = false
                
                // Store deleted id to array.(Only if id is positive.)
                arrDeletaedTimeId.append("\(selectedTimeId!)")
                break
            }
        }
        sortArrayTimings()
        if isEmptyCell {
            // If want to remove empty cell.
            nEmptyRow = 0
        }
        
        tblviewTimings.beginUpdates()
        selectedCell.nsLLeadeingSpace.constant = 0
        tblviewTimings.deleteRows(at: [selectedCell.indexPath], with: .fade)
        tblviewTimings.endUpdates()
        self.perform(#selector(reloadTable), with: nil, afterDelay: 0.5)
    }
    
    @objc func reloadTable() {
        DispatchQueue.main.async {
            self.tblviewTimings.reloadData()
        }
    }
    
    func addArrayTimings() {
        // Calculate time id for adding new task time.
        // Provide negative time id if it is created by user.
        // This can be idenfied easily while uploading to server database.
        if nil != timeId {
            timeId -= 1
        }
        else {
            let taskTimeCDCtrlr = TasksTimeCDController()
            timeId = taskTimeCDCtrlr.getTimeIdToAddNewTaskTime()
        }
        selectedCell.timeId = timeId
        let date = selectedCell.lblDate.text!
        let startTime = getSecondCount(strTime: selectedCell.lblStartTime.text!)
        let endTime = getSecondCount(strTime: selectedCell.lblEndTime.text!)
        let strDescr = selectedCell.txtFDescription.text!
        let cTaskTimeDetails = TaskTimeDetails(timeId: timeId, date: date, start: startTime,
                                               end: endTime, descr: strDescr)
        
        arrTaskTimeDetails.append(cTaskTimeDetails)
        sortAndDisplayTimings()
        // After adding new row, empty filled rows are zero.
        nEmptyRow = 0
    }
    
    /// To show date time picker with animation
    func showDateTimePicker() {
        viewBG.isHidden = false
        nsLDatePickerHeight.constant = cgFHeightForPopup
        UIView.animate(withDuration: g_nAnimatnDuratn) {
            self.view.layoutIfNeeded()
        }
        self.view.endEditing(true)
    }
    
    /// To hide date time picker with animation.
    func hideDateTimePicker() {
        viewBG.isHidden = true
        nsLDatePickerHeight.constant = 0
        UIView.animate(withDuration: g_nAnimatnDuratn+0.1) {
            self.view.layoutIfNeeded()
        }
    }
    
    /// If edit or add time already exist show alert .
    func alertUnableToUpdate() {
        let alert = UIAlertController(title: "Alert", message:
            "Enter valid time. Updation failed", preferredStyle:
            UIAlertController.Style.alert)
        alert.addAction(UIAlertAction(title: "OK", style: UIAlertAction.Style.default, handler: nil
        ))
        self.present(alert, animated: true, completion: nil)
    }
    
    /// If time already exist.
    func alertUnableToChangeDate(type: TimeError, startTime: String? = nil, endTime: String? = nil) {
        let strError: String!
        switch type {
            case .futureTime:
                strError = "You should not enter the future timings"
            case .startGreater:
                strError = "Start time should not be greater than end time"
            case .timeExist:
                strError = "Time you have entered is already exists"
            case .punchedout:
                strError =
                "Since you are punched out, You will be not able to start a task, please enter end time if you want to update the timings"
            case .punchin:
                strError =
            "Since you are not punched in, You will be not able to start a task, please enter end time if you want to update the timings"
            case .outOfWorkTime:
                if nil != startTime {
                    strError =
                    "Start/End time should be within the punch in/out timings(\(startTime!)-\(endTime ?? "no punch out"))"
                }
                else {
                    strError =
                    "Date you have entered does not contain your punch in/out data"
                }
            default:
                strError = "Enter valid time"
        }
        let alert = UIAlertController(title: "Alert", message:
            strError, preferredStyle:
            UIAlertController.Style.alert)
        alert.addAction(UIAlertAction(title: "OK", style: UIAlertAction.Style.default, handler: { _ in
                // If updation failed set end field empty
            if nil != self.selectedCell {
                self.selectedCell.lblEndTime.text = ""
            }
        }
        ))
        self.present(alert, animated: true, completion: nil)
    }
    
    func alertSuccessUpdate() {
        let alert = UIAlertController(title: "Success", message:
            "Updation Successfull", preferredStyle:
            UIAlertController.Style.alert)
        alert.addAction(UIAlertAction(title: "OK", style: UIAlertAction.Style.default, handler: {
            _ in
            self.sortAndDisplayTimings()
            }
        ))
        self.present(alert, animated: true, completion: nil)
    }
    
    /// Removing row success message.
    func alerRemoveRowSuccess() {
        let alert = UIAlertController(title: "Success", message:
            "Successfully removed a task time", preferredStyle:
            UIAlertController.Style.alert)
        alert.addAction(UIAlertAction(title: "OK", style: UIAlertAction.Style.default, handler:
            {
                _ in
                self.sortAndDisplayTimings()
        }))
        self.present(alert, animated: true, completion: nil)
    }
    
    @objc func viewBGPressed(sender: UITapGestureRecognizer) {
        if nsLDatePickerHeight.constant == cgFHeightForPopup {
            // Update date to the table cell and validate.
//            editDateTimeCompleted()
            if arrTaskTimeDetails.count > selectedCell.indexPath.row {
                arrTaskTimeDetails[selectedCell.indexPath.row].description =
                    selectedCell.txtFDescription.text
            }
            hideDateTimePicker()
        }
        setCellToConstantPosition()
        
        // Hide project and module selection table.
        hideTableProjectModule()
        
        // If keyboard displayed end eding.
        self.view.endEditing(true)
    }
    
    @objc func scrollViewPressed(sender: UITapGestureRecognizer) {
        // If keyboard displayed end eding.
        self.view.endEditing(true)
        setCellToConstantPosition()
        updateTextDescription()
    }
    
    /// View pressed if any cell description chnaged update it.
    func updateTextDescription() {
        if nil != selectedCell && arrTaskTimeDetails.count > selectedCell.indexPath.row {
            arrTaskTimeDetails[selectedCell.indexPath.row].description =
                selectedCell.txtFDescription.text
        }
    }
    
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
        // If indexpath is for add time cell.
        if tblviewTimings == tableView {
            if tableView.numberOfRows(inSection: 0)-1 == indexPath.row {
                return 44
            }
            else {
                return cellHeight
            }
        }
        else {
            return 44
        }
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
    
    // Update view and database while moving to activity view.
    func updateActivityView() {
        // Check for quick action.
        if true == bIsQuickAction {
            self.presentingViewController?.dismiss(animated: true, completion: {self.view = nil})
            let mainStoryboardIpad : UIStoryboard = UIStoryboard(name: "Main", bundle: nil)
            let initialViewController = mainStoryboardIpad.instantiateViewController(withIdentifier:
                "LoginViewController") as! LoginViewController
            initialViewController.nSleepTime = 0
            let appDelegate = UIApplication.shared.delegate as! AppDelegate
            appDelegate.window?.rootViewController = initialViewController
        }
        else {
            if let tabBarController = self.navigationController?.viewControllers[0]
                as? TabBarController {
                if let viewActivity = tabBarController.selectedViewController as? UserActivityVC {
//                    if !(RequestController.shared.reachabilityManager?.isReachable)! {
//                        viewActivity.updateView()
//                    }
//                    else {
                        viewActivity.updateProject()
                        viewActivity.sortAndRefreshData()
                        viewActivity.arrRunningTask = viewActivity.getRunningTaskId()
                        // If no task running draw play icon or stop icon.
                        viewActivity.drawTaskState()
                        //                    viewActivity.splitMergeStateAndFinishBtn()
                        viewActivity.collectionTimer.reloadData()
//                    }
                    self.navigationController?.popToRootViewController(animated: true)
                }
            }
            else {
                // Iniialise from task history VC.
                APIResponseHandler.loadTaskDetails(pageNo: g_taskPageNo, completion: {
                    status in
                    if status {
                        // Refresh dashboard.
                        if let tabBarController = (self.presentingViewController
                            as! UINavigationController).viewControllers[0] as? TabBarController {
                            
                            // Update activity view.
                            if let viewActivity = tabBarController.viewControllers![0]
                                as? MyActivityViewController {
                                viewActivity.updateCoredataTimings()
                                viewActivity.arrActView[0].setupDayView()
                                viewActivity.arrActView[1].resetWeekBar()
                                viewActivity.arrActView[1].setupWeekView()
                                viewActivity.arrActView[2].setupMonthView()
                                self.dismiss(animated: true, completion: nil)
                            }
                            if let viewActivity = tabBarController.viewControllers![1]
                                as? UserActivityVC {
                                viewActivity.updateProject()
                                viewActivity.arrRunningTask = viewActivity.getRunningTaskId()
                                // If no task running draw play icon or stop icon.
                                viewActivity.drawTaskState()
                                viewActivity.collectionTimer.reloadData()
                            }
                        }
                    }}
                )
            }
        }
    }
    
    func updateTimingsToDatabase(startTask: Bool = false, completion:@escaping (Bool) -> ()) {
        // If device connected to internet.
        if (RequestController.shared.reachabilityManager?.isReachable)! {
            actIndicator.startAnimating()

            // Add news task.
            APIResponseHandler.addTask(params: getParameter(), completion: {
                (status, id, msg) in
                if status {
                    // If any timings removed then, remove it from core data too.
                    for timeId in self.arrDeletaedTimeId {
                        let taskTimeCDCtrlr = TasksTimeCDController()
                        taskTimeCDCtrlr.deleteTaskTime(timeId: Int(timeId)!)
                    }

                    // Start task if start button action.
                    if startTask {
                        APIResponseHandler.startTaskOrPunchIn(taskId: "\(id)",completion: {
                            status, msg  in
                            if status {
                                completion(true)
                            }
                            else {
                                completion(false)
                            }
                        })
                    }
                        // don't start.
                    else {
                        completion(true)
                    }
                }
                else {
                    print("Error while updating")
                    self.lblError.text = msg
                    completion(false)
                }
            })
        }
        else {
            if nil == taskId {
                // Adding new task.
                var strTaskDescr = ""
                if "Task description" != txtVTaskDescr.text {
                    strTaskDescr = txtVTaskDescr.text!
                }
                var isRunning = false
                var nStartTime: Int64 = 0
                
                // Get start time.
                if arrTaskTimeDetails.count > 0 {
                    // From array timings
                    let cTaskTimeDetails = arrTaskTimeDetails[0]
                    
                    let time = getSecondsToHoursMinutesSeconds(seconds: cTaskTimeDetails
                        .nStartTime)
                    let strDate = convertStrDateFormate2(strDate: cTaskTimeDetails.strDate!)
                    let date = convertUTCtoLocal(strDateTime: "\(strDate) \(time)")
                    nStartTime = date.millisecondsSince1970
                    if nEmptyRow == 1 {
                        isRunning = true
                    }
                }
                if nEmptyRow == 1 {
                    // Else if available start time first index.
                    let lastCell = tblviewTimings.cellForRow(at: [0, arrTaskTimeDetails.count])
                        as! TimingsCell
                    isRunning = true
                    let time = convert12to24Format(strTime: lastCell.lblStartTime.text!)
                    let strDate = convertStrDateFormate2(strDate: lastCell.lblDate.text!)
                    let date = convertUTCtoLocal(strDateTime: "\(strDate) \(time)")
                    nStartTime = date.millisecondsSince1970
                }
                taskId = tasksCDController.addNewTask(projectId: selectedProjId, taskName:
                    txtTaskName.text!, taskDesc: strTaskDescr, moduleId: selectedModId
                    ,isSynched: false, isRunning: isRunning, startTime: nStartTime)
            }
            else {
                // Editing selected task.
                var startTime: Int64 = 0
                if arrTaskTimeDetails.count > 0 {
                    // Set task start time.
                    let strStartDate = arrTaskTimeDetails[0].strDate! // Start day and time will be in
                    let nStartTime = arrTaskTimeDetails[0].nStartTime! // first index of timings array.
                    let dateStart = Date().getDateFromStrDateAndIntTime(strDate: strStartDate, nTime:
                        nStartTime)
                    startTime = dateStart.millisecondsSince1970
                }
                tasksCDController.updateTaskNameDescrAndProject(taskId: taskId!, moduleId: selectedModId,
                    strTaskName: txtTaskName.text!, strDescr: txtVTaskDescr.text, projectId:
                    selectedProjId, isWorking: false, startTime: startTime, isSynched: false, deleted:
                arrDeletaedTimeId)
            }
            let taskTimeCDCtrl = TasksTimeCDController()
            if arrTaskTimeDetails.count > 0 {
                for cTaskTimeDetails in arrTaskTimeDetails {
                    let timeId = cTaskTimeDetails.timeId!
                    let strDate = cTaskTimeDetails.strDate!
                    let nStartTime = cTaskTimeDetails.nStartTime!
                    let nEndTime = cTaskTimeDetails.nEndTime!
                    let descriptn = cTaskTimeDetails.description ?? ""
                    
                    taskTimeCDCtrl.addOrUpdateTaskTimings(timeId: timeId, taskId: taskId!, strDate:
                        strDate, startTime: nStartTime, endTime: nEndTime, descr: descriptn)
                }
            }
            // Check for last field filled with date and start time
            // To indicate the task should start.
            if nEmptyRow == 1 {
                let lastCell = tblviewTimings.cellForRow(at: [0, arrTaskTimeDetails.count])
                    as! TimingsCell
                let strDate = lastCell.lblDate.text!
                let strStartTime = lastCell.lblStartTime.text!
                let strEndTime = lastCell.lblEndTime.text!
                if "" == strEndTime && "" != strDate && "" != strStartTime {
                    // Convert to valid format.
                    let nStartTime = getSecondCount(strTime: strStartTime)
                    
                    if nil != timeId {
                        timeId -= 1
                    }
                    else {
                        let taskTimeCDCtrlr = TasksTimeCDController()
                        timeId = taskTimeCDCtrlr.getTimeIdToAddNewTaskTime()
                    }
                    let descriptn = lastCell.txtFDescription.text
                    taskTimeCDCtrl.addOrUpdateTaskTimings(timeId: timeId!, taskId: taskId!, strDate:
                        strDate, startTime: nStartTime, endTime: nStartTime, descr: descriptn)
                }
            }
            completion(true)
        }
    }
    
    func getParameter() -> Dictionary<String, Any> {
        var dictParams = Dictionary<String, Any>()
        let strUserId = UserDefaults.standard.value(forKey: "userId") as! String
        dictParams.updateValue(strUserId, forKey: "userid")
        dictParams.updateValue(txtTaskName.text!, forKey: "task_name")
        if "Task description" == txtVTaskDescr.text {
            // Default text.
            dictParams.updateValue("", forKey: "task_desc")
        }
        else {
            dictParams.updateValue(txtVTaskDescr.text!, forKey: "task_desc")
        }
        dictParams.updateValue("\(selectedProjId!)", forKey: "project_id")
        dictParams.updateValue("\(selectedModId!)", forKey: "project_module")
        
        var arrDictTimings = Array<Any>()
            for cTaskTimeDetails in arrTaskTimeDetails {
                let strDate = cTaskTimeDetails.strDate!
                
                let nStartTime = cTaskTimeDetails.nStartTime!
                let nEndTime = cTaskTimeDetails.nEndTime!
                let strStartTime = getSecondsToHoursMinutesSeconds(seconds: nStartTime)
                
                let strDateToAPI = convertLocalDateToUTC(strDate: "\(strDate) \(strStartTime)"
                    , format: "dd/MM/yyyy HH:mm:ss")
                let strStartDateTime = convertLocalTimeToUTC(strDateTime: "\(strDate) \(strStartTime)")
                
                var strEndDateTime: String!
                // When last field of end timw set to zero. to start task.
                if nEndTime == 0 {
                    strEndDateTime = ""
                }
                else {
                    let strEndTime = getSecondsToHoursMinutesSeconds(seconds: nEndTime)
                    strEndDateTime = convertLocalTimeToUTC(strDateTime: "\(strDate) \(strEndTime)")
                }
                let descriptn = cTaskTimeDetails.description!
                
                var dictTimings: Dictionary<String, Any>!
                if cTaskTimeDetails.timeId > 0 {
                    // If time id id from server DB.
                    // Edit task timings.
                    dictTimings = ["date":strDateToAPI, "start": strStartDateTime,
                                       "end": strEndDateTime!,"task_description": descriptn,
                    "table_id": "\(cTaskTimeDetails.timeId!)"]
                }
                else {
                    // Otherwise, append new task time to server db.
                    dictTimings = ["date": strDateToAPI, "start": strStartDateTime, "end"
                        : strEndDateTime!, "task_description": descriptn]
                }
                arrDictTimings.append(dictTimings!)
            }
            
            // Check for last field filled with date and start time
            // To indicate the task should start.
            if nEmptyRow == 1 {
                let lastCell = tblviewTimings.cellForRow(at: [0, arrDictTimings.count]) as! TimingsCell
                let strDate = lastCell.lblDate.text!
                var strStartTime = lastCell.lblStartTime.text!
                let strEndTime = lastCell.lblEndTime.text!
                if "" == strEndTime && "" != strDate && "" != strStartTime {
                    
                    // Convert to valid format.
                    let nStartTime = getSecondCount(strTime: strStartTime)
                    strStartTime = getSecondsToHoursMinutesSeconds(seconds: nStartTime)
                    
                    let strCurrentDate = getCurrentDateTime()
                    // Check for valid time.
                    if Date(strDateTime: strCurrentDate) >=
                            Date(strDateTime: "\(strDate) \(strStartTime)") {
                        let strDateToAPI = convertLocalDateToUTC(strDate: "\(strDate) \(strStartTime)"
                            , format: "dd/MM/yyyy HH:mm:ss")
                        let strStartDateTime = convertLocalTimeToUTC(strDateTime: "\(strDate) \(strStartTime)")
                        let descriptn = lastCell.txtFDescription.text!
                        
                        let dictTimings = ["date": strDateToAPI, "start": strStartDateTime, "end"
                            : "", "task_description": descriptn]
                        arrDictTimings.append(dictTimings)
                    }
                }
            }
        
        // Add task id if it is edit case.
        if let id = taskId {
            dictParams.updateValue("\(id)", forKey: "task_id")
            // Get only server timid.
            var arrDelTimeId = Array<String>()
            for timeId in arrDeletaedTimeId {
                if Int(timeId)! > 0 {
                    arrDelTimeId.append(timeId)
                }
            }
            dictParams.updateValue(arrDelTimeId, forKey: "deleted_time_range")
        }
        dictParams.updateValue(arrDictTimings, forKey: "time_range")

        return dictParams
    }
    
    @IBAction func btnBackPressed(_ sender: Any) {
        // If navigated from task history vc.
        if (self.presentingViewController as! UINavigationController).viewControllers[0]
            is UITabBarController {
            self.dismiss(animated: true, completion: nil)
            return
        }
        updateActivityView()
    }
    
    func errorMessage(msg: String) {
        lblError.shakeLabel()
        lblError.text = msg
        lblError.isHidden = false
        scrollView.scrollToTop()
    }
    
    // Check all the cell fields are filled except last row.
    func checkAllTimingsFilled() -> Bool {
        let lastIndex = nEmptyRow == 1 ? arrTaskTimeDetails.count : arrTaskTimeDetails.count - 1
        if lastIndex >= 0 {
            for index in 0..<lastIndex {
                let cell = tblviewTimings.cellForRow(at: [0, index]) as! TimingsCell
                if cell.lblDate.text == "" || cell.lblStartTime.text == "" ||
                    cell.lblEndTime.text == "" {
                    errorMessage(msg: "Enter timings")
                    return false
                }
            }
        }
        return true
    }
    
    @IBAction func btnSavePressed(_ sender: Any) {
        guard !(txtTaskName.text == "") && nil != selectedProjId && nil != selectedModId
            && checkAllTimingsFilled() else {
                    errorMessage(msg: "Fill all the fields")
                    return
        }
        
        updateTextDescription()
        
        // Check for empty cell is filled with date and start time.
        let lastIndex = nEmptyRow == 1 ? arrTaskTimeDetails.count : arrTaskTimeDetails.count - 1
        if lastIndex >= 0 {
            let cell = tblviewTimings.cellForRow(at: [0, lastIndex]) as! TimingsCell
            if cell.lblDate.text == "" || cell.lblStartTime.text == "" {
                errorMessage(msg: "Fill date and start time")
                return
            }
            // Check for punch in/out time if end time not mentioned.
            else if cell.lblEndTime.text == "" {
                let strDate = cell.lblDate.text
                let strTime = cell.lblStartTime.text
                
                // Validate time in between working hour.(Punch in/out)
                let startDate = convertStrDateTimeToDate(strDateTime:
                    "\(strDate!) \(strTime!)", format: "dd/MM/yyyy h:mm a")
                
                let punchInOutCDCtrlr = PunchInOutCDController()
                let (isOutOfrange, strStart, strEnd) = punchInOutCDCtrlr
                    .isTimeExistInPunchInOut(start:
                    startDate)
                if !isOutOfrange {
                    return alertUnableToChangeDate(type: .outOfWorkTime, startTime: strStart
                        , endTime: strEnd)
                }
            }
            
            // Check for future time entered with end time blank.
            if cell.lblEndTime.text == "" && cell.lblDate.text == getCurrentDate()
                && getTimeInSec() < getSecondCount(strTime: cell.lblStartTime.text!) {
                errorMessage(msg: "Start time cannot be future time")
                return
            }
            
            // Check for punched out and punched in.
            if !g_isPunchedOut && (g_isPunchedIn ?? false) {
                // Check for end time not containing.
                if cell.lblEndTime.text == "" {
                    if nEmptyRow == 0 {
                        // Remove its end time(Since arrTimings not updated).
                        arrTaskTimeDetails[lastIndex].nEndTime = 0
                    }
                    if false == UserDefaults.standard.value(forKey: "multi_task") as? Bool {
                        // If any tasks are running stop those tasks.
                        if let tabBarController = self.navigationController?.viewControllers[0]
                            as? TabBarController {
                            if let userActivityVC = tabBarController.selectedViewController
                                as? UserActivityVC {
                                userActivityVC.stopRunningTask(stopAll: true, completion: {
                                    userActivityVC.updateProject()
                                })
                            }
                        }
                    }
                }
            }
            else if cell.lblEndTime.text == "" {
                if g_isPunchedOut {
                    alertUnableToChangeDate(type: .punchedout)
                }
                else {
                    alertUnableToChangeDate(type: .punchin)
                }
                return
            }
        }
        
        // Disable buttons.
        btnStart.isEnabled = false
        btnSave.isEnabled = false
        view.isUserInteractionEnabled = false
        
        // Check for new task && start task (without end time).
        
        lblError.isHidden = true
        updateTimingsToDatabase(completion: {
            status in
            if status {
                self.updateActivityView()
                self.actIndicator.stopAnimating()
            }
            else {
                self.lblError.isHidden = false
                self.actIndicator.stopAnimating()
                self.scrollView.scrollToTop()
            }
            // Enable buttons.
            self.btnStart.isEnabled = true
            self.btnSave.isEnabled = true
            self.view.isUserInteractionEnabled = true
        })
    }
    
    @IBAction func btnStartPressed(_ sender: Any) {
        guard !(txtTaskName.text == "") && nil != selectedProjId && nil != selectedModId else {
            lblError.text = "Fill all the fields"
            lblError.isHidden = false
            scrollView.scrollToTop()
            return
        }
        // Disable buttons.
        btnStart.isEnabled = false
        btnSave.isEnabled = false
        
        lblError.isHidden = true
        updateTimingsToDatabase(startTask: true, completion: {
            status in
            if status {
                self.updateActivityView()
                self.actIndicator.stopAnimating()
            }
            else {
                self.updateActivityView()
                self.actIndicator.stopAnimating()
            }
            // Enable buttons.
            self.btnStart.isEnabled = true
            self.btnSave.isEnabled = true
        })
    }
    
    @IBAction func txttaskNamePrimaryAction(_ sender: Any) {
        txtVTaskDescr.becomeFirstResponder()
    }
    
    func textView(_ textView: UITextView, shouldChangeTextIn range: NSRange, replacementText text:
        String) -> Bool {
        if (text == "\n") {
            textView.resignFirstResponder()
        }
        return true
    }
    
    @IBAction func viewPressed(_ sender: Any) {
        self.view.endEditing(true)
        setCellToConstantPosition()
        updateTextDescription()
    }
    
    deinit {
        print("TaskController deinitialized")
    }
    
    override func traitCollectionDidChange(_ previousTraitCollection: UITraitCollection?) {
        super.traitCollectionDidChange(previousTraitCollection)
        
        guard UIApplication.shared.applicationState == .inactive else {
            return
        }
        
        if #available(iOS 12.0, *) {
            if self.traitCollection.userInterfaceStyle == .light {
                UserDefaults.standard.setValue(1, forKey: "colorMode")
            }
            else {
                UserDefaults.standard.setValue(2, forKey: "colorMode")
            }
            // Remove old gradients.
            self.view.layer.sublayers?.removeFirst()
            self.btnStart.layer.sublayers?.removeFirst()
            
            setColorMode()
            updateGradient()
        }
    }
    
    func updateGradient() {
        if let tabController = self.navigationController?.viewControllers[0] as? TabBarController {
            tabController.updateAllViewCtrlrs()
        }
        
        view.addGradient(cgPStart: CGPoint(x: 0, y: 0), cgPEnd: CGPoint(x: 1, y: 0.3))
        btnStart.addGradient(cgFRadius: 22)
        tblviewTimings.backgroundColor = g_colorMode.defaultColor()
        view.layer.needsLayout()
    }
    
    func textViewDidBeginEditing(_ textView: UITextView) {
        // To clear textview placeholder.
        if textView.textColor == g_colorMode.midText() {
            textView.text = ""
            textView.textColor = g_colorMode.textColor()
        }
    }
    
    func textViewDidEndEditing(_ textView: UITextView) {
        // To add textview placeholder.
        if textView.text.isEmpty {
            textView.text = "Task description"
            textView.textColor = g_colorMode.midText()
        }
    }
}
