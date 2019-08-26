//
//  ActivityController.swift
//  Attendance Manager
//
//  Created by Sachin on 9/4/19.
//  Copyright © 2019 Sachin. All rights reserved.
//

import UIKit
import CoreData

let cgCForGradientStart = UIColor(red: 100/255, green: 105/255, blue: 255/255, alpha: 1.0).cgColor
let cgCForGradientEnd = UIColor(red: 240/255, green: 128/255, blue: 245/255, alpha: 1.0).cgColor

enum SortTypes: String {
    case projects = "Project Id"
    case tasks = "Task Id"
    case duration = "Total Time"
}

protocol IndexValueDelegate {
    func selectedIndex(indexPath: IndexPath)
}

class ActivityController: UIViewController, UITableViewDelegate, UITableViewDataSource,
    TableHeaderViewDelegate, UIGestureRecognizerDelegate, FilterDelegate {
    @IBOutlet weak var btnState: UIButton!
    @IBOutlet weak var lblTimer: UILabel!
    @IBOutlet weak var lblLogin: UILabel!
    @IBOutlet weak var btnLogout: UIButton!
    @IBOutlet weak var tblUserDetails: UITableView!
    @IBOutlet weak var lblTimer2: UILabel!
    @IBOutlet weak var viewProfile: UIView!
    @IBOutlet weak var btnAbout: UIButton!
    @IBOutlet weak var lblUserName: UILabel!
    @IBOutlet weak var btnActivities: UIButton!
    @IBOutlet weak var btnAddTask: UIButton!
    @IBOutlet weak var imgProfile: UIImageView!
    @IBOutlet weak var lblTaskTitle: UILabel!
    @IBOutlet weak var nsLProfileViewWidth: NSLayoutConstraint!
    @IBOutlet weak var btnProfile: UIButton!
    
    var timer: Timer? = Timer()
    var strUserName = "Sachin Salian"
    var strTimeLabel = 0
    var strTimeLabelCell = 0
    var date: String!
    var bIsProfileSlideOut = false
    var userActUpdater: UserActivityUpdater!
    var projectUpdater: AddProjects!
    var taskUpdater: TaskUpdater!
    var nSelectedId: Int!
    var bIsFilterHidden = true
    var arrSelectedProj: Array<String>?
    var indexSelectedSort: IndexPath!
    var indexForTimer: IndexPath?
    var arrDictTaskDetails: Array<Dictionary<String, Any>>!
    let dictProjects = ["Sphere" : "https://www.gstatic.com/webp/gallery3/3.png", "Buck"
        : "https://www.gstatic.com/webp/gallery3/5.png", "New Project"
            : "https://www.gstatic.com/webp/gallery3/2.png"]
    var strSortType: String!
    override func viewDidLoad() {
        super.viewDidLoad()
        
        date = getDate()
        self.tblUserDetails.layer.masksToBounds = true
        self.tblUserDetails.layer.borderWidth = 3.0
        self.tblUserDetails.layer.borderColor = UIColor.white.cgColor
        tblUserDetails.roundCorners(corners: [.topLeft, .topRight], radius: 35.0)
        
        self.tblUserDetails.layer.cornerRadius = 35.0
        tblUserDetails.delegate = self
        tblUserDetails.dataSource = self
        tblUserDetails.register(UINib(nibName: "userBreakInfoCell", bundle: nil),
                                forCellReuseIdentifier: "userBreakInfoCell")
        lblTimer.isHidden = true
        btnState.isHidden = true
        btnLogout.isHidden = true
        tblUserDetails.isHidden = true
        tblUserDetails.scrollIndicatorInsets = UIEdgeInsets(top: 80, left: 0, bottom: 0, right: 0)
        tblUserDetails.rowHeight = UITableView.automaticDimension
//        tblUserDetails.contentInset.top = btnState.frame.minY
//        tblUserDetails.contentOffset.y = -btnState.frame.minY
        
//        tblUserDetails.contentInset = UIEdgeInsets(top: nsLtableTop.constant, left: 0, bottom: 0, right: 0)
        
        
        
//        btnLogout.setTitleColor(UIColor(cgColor: cgCForGradientStart), for: .normal)
//        btnActivities.setTitleColor(UIColor(cgColor: cgCForGradientStart), for: .normal)
//        btnAbout.setTitleColor(UIColor(cgColor: cgCForGradientStart), for: .normal)
        userActUpdater = UserActivityUpdater()
        projectUpdater = AddProjects()
        taskUpdater = TaskUpdater()
        btnState.setUpButton()
        strSortType = SortTypes.tasks.rawValue
        arrDictTaskDetails = Array()
        createNewDate()
        userActUpdater.fetchAllData()
        projectUpdater.fetchAllData()
        taskUpdater.fetchAllData()
        minHeaderHeight = -UIScreen.main.bounds.height * 0.1
        maxHeaderHeight = nsLtableTop.constant
        nsLConstantTop = nsLtableTop.constant
        for (key, value) in dictProjects {
            if !projectUpdater.isProjectExist(projectName: key) {
                projectUpdater.addNewProject(projectName: key, projectIconUrl: value)
            }
            else {
                print("Exists")
            }
        }
        NotificationCenter.default.addObserver(self, selector: #selector(viewToForeground),
                            name: UIApplication.willEnterForegroundNotification, object: nil)
       
        if !userActUpdater.IsUserTerminatedWork() {
//          If user currently working in the office.
            updateArrayDetails()
            tblUserDetails.reloadData()
        }
    }
    
    func createNewDate() {
        if userActUpdater.isTodayDateExists() {
            if !userActUpdater.IsUserInBreak() {
                userActUpdater.updateUSerWorkTime()
                taskUpdater.updateUserTaskTime()
            }
        }
        else {
            userActUpdater.createNewDate()
        }
    }
    
    override func viewDidAppear(_ animated: Bool) {
        super.viewDidAppear(true)
        if userActUpdater.IsUserTerminatedWork() {
            //If user finished his work and logged out.
            let alert = UIAlertController(title: "Alert!!", message:
                "You can't login..!! Coz, your already finished your today's work..!",
                                preferredStyle: UIAlertController.Style.alert)
                           
            alert.addAction(UIAlertAction(title: "Back", style: UIAlertAction.Style.default,
                                                         handler: { _ in
                if let viewLogin = self.presentingViewController as? LoginController {
                    viewLogin.nSleepTime = 0
                }
                self.view.window!.rootViewController?.dismiss(animated: false, completion:
                {
                    self.view = nil
                })
            }))
            self.present(alert, animated: true, completion: nil)
        }
        else {
            self.view.addGradient(startColor: cgCForGradientStart, endColor: cgCForGradientEnd,
            cgPStart: CGPoint(x: 0, y: 0), cgPEnd: CGPoint(x: 1, y: 0.5))
            setUpView()
        }
        
    }
    
    func sortAndRefreshData() {
        if let arrProj = arrSelectedProj {
            arrDictTaskDetails = taskUpdater.getTaskDetailsFromProjectName(arrProj: arrProj)
        }
        else {
            let arrProj = projectUpdater.getAllProjectNames()
            arrDictTaskDetails = taskUpdater.getTaskDetailsFromProjectName(arrProj: arrProj)
        }
        arrDictTaskDetails.sort { (task1, task2) -> Bool in
            return (task1[strSortType] as! Int) > (task2[strSortType] as! Int)
        }
        tblUserDetails.reloadData()
        if arrDictTaskDetails.count > 0 {
            tblUserDetails.scrollToRow(at: [0, 0], at: .top, animated: true)
        }
    }
    
    func updateArrayDetails() {
        if let arrProj = arrSelectedProj {
            arrDictTaskDetails = taskUpdater.getTaskDetailsFromProjectName(arrProj: arrProj)
        }
        else {
            let arrProj = projectUpdater.getAllProjectNames()
            arrDictTaskDetails = taskUpdater.getTaskDetailsFromProjectName(arrProj: arrProj)
        }
        arrDictTaskDetails.sort { (task1, task2) -> Bool in
            return (task1[strSortType] as! Int) > (task2[strSortType] as! Int)
        }
    }
    
    @objc private func viewToForeground() {
        createNewDate()
        strTimeLabel = userActUpdater.getTotalTime()
        let strTime = getSecondsToHourMinute(seconds: userActUpdater.getLoginTime())
        let strLogin = "Started at \(convert24to12FormatHourMinute(strTime: strTime))"
        lblLogin.text = strLogin
        if let arrProj = arrSelectedProj {
            arrDictTaskDetails = taskUpdater.getTaskDetailsFromProjectName(arrProj: arrProj)
        }
        else {
            let arrProj = projectUpdater.getAllProjectNames()
            arrDictTaskDetails = taskUpdater.getTaskDetailsFromProjectName(arrProj: arrProj)
        }
        sortAndRefreshData()
    }
    
    func setUpView() {
        btnState.isHidden = false
        btnLogout.isHidden = false
        lblTimer.isHidden = false
        tblUserDetails.isHidden = false
        UserDefaults.standard.setValue(true, forKey: "userLogin")
        if let strName = UserDefaults.standard.string(forKey: "Username") {
            strUserName = strName
        }
        lblUserName.text = strUserName
        if userActUpdater.IsUserInBreak() {
            strTimeLabel = userActUpdater.getTotalTime()
            btnState.setTitle("Start", for: .normal)
            btnState.drawPlay(layerHeight: btnState.bounds.width * 0.3)
            lblTimer.text = "\(getSecondsToHoursMinutesSeconds(seconds: strTimeLabel))"
            lblTimer2.text = "\(getSecondsToHoursMinutesSeconds(seconds: strTimeLabel))"
        }
        else {
            btnState.setTitle("Stop", for: .normal)
            btnState.drawStop(layerHeight: btnState.bounds.width * 0.25)
            userActUpdater.updateUSerWorkTime()
            taskUpdater.updateUserTaskTime()
            runTime()
        }
        strTimeLabel = userActUpdater.getTotalTime()
        let strTime = getSecondsToHourMinute(seconds: userActUpdater.getLoginTime())
        
        let strLogin = "Started at \(convert24to12FormatHourMinute(strTime: strTime))"
        lblLogin.text = strLogin
        updateArrayDetails()
        if let index = indexForTimer?.row {
            print("heheiheiehi")
            let dictValue = arrDictTaskDetails[index]
            let nTime = dictValue["Total Time"] as! Int
            strTimeLabelCell = nTime
        }
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
            return arrDictTaskDetails.count
    }
    
    func numberOfSections(in tableView: UITableView) -> Int {
        return 1
    }
    
    func toogleSection(section: Int) {
        tblUserDetails.reloadData()
    }
    
    func tableView(_ tableView: UITableView, heightForHeaderInSection section: Int) -> CGFloat {
            return 80
    }
    
    @objc func btnFilterPressed(_ sender: Any) {
        print("giusfgdigduu")
        if bIsFilterHidden {
            bIsFilterHidden = false
            performSegue(withIdentifier: "ShowFilter", sender: nil)
            self.view.alpha = 0.5
        }
    }
    
    
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        if segue.destination is FilterViewController {
            let vc = segue.destination as? FilterViewController
            vc?.delegate = self
            if let arrProj = arrSelectedProj {
                vc?.arrSelectedProj = Array<String>()
                vc?.arrSelectedProj = arrProj
            }
            if let index = indexSelectedSort {
                vc?.indexSelected = index
            }
        }
       else if segue.identifier == "SegueToTaskController" {
            let taskController = segue.destination as! TaskController
            taskController.taskId = nSelectedId
        }
    }
    
    func selectedProjects(arrProj: Array<String>) {
        bIsFilterHidden = true
        arrSelectedProj = Array<String>()
        arrSelectedProj = arrProj
        arrDictTaskDetails = taskUpdater.getTaskDetailsFromProjectName(arrProj: arrSelectedProj!)
        sortAndRefreshData()
        self.view.alpha = 1
    }
    
    func dismissedView() {
        bIsFilterHidden = true
        self.view.alpha = 1
    }
    
    func clearedFilter() {
        bIsFilterHidden = true
        arrSelectedProj = nil
        indexSelectedSort = nil
        strSortType = SortTypes.tasks.rawValue
        let arrProj = projectUpdater.getAllProjectNames()
        arrDictTaskDetails = taskUpdater.getTaskDetailsFromProjectName(arrProj: arrProj)
        sortAndRefreshData()
        self.view.alpha = 1
    }
    
    func sortApplied(strSortType: String, indexSelected: IndexPath) {
        self.strSortType = strSortType
        indexSelectedSort = indexSelected
        bIsFilterHidden = true
        self.view.alpha = 1
        sortAndRefreshData()
    }
    
    func scrollViewDidScroll(_ scrollView: UIScrollView) {
        updateHeaderView()
    }
    
    @IBOutlet weak var nsLtableTop: NSLayoutConstraint!
    @IBOutlet weak var nsLTableTopWidth: NSLayoutConstraint!
    var maxHeaderHeight: CGFloat!
    var minHeaderHeight: CGFloat!
    var nsLConstantTop: CGFloat!

    func updateHeaderView() {
        guard let tblUserDetails = tblUserDetails else {
            return
        }

        if (tblUserDetails.contentOffset.y + minHeaderHeight) < maxHeaderHeight {
            nsLtableTop.constant = maxHeaderHeight - tblUserDetails.contentOffset.y
            let progress1 = (nsLtableTop.constant - minHeaderHeight)
                / (maxHeaderHeight - minHeaderHeight)
            let progress2 = (maxHeaderHeight - nsLtableTop.constant)
                / (maxHeaderHeight - minHeaderHeight)
            lblTimer.alpha = progress1
            lblTaskTitle.alpha = progress1
            lblTimer2.alpha = progress2
        }
        else {
            nsLtableTop.constant = minHeaderHeight
        }
    }
    
    func tableView(_ tableView: UITableView, viewForHeaderInSection section: Int) -> UIView? {
        let header = TableHeaderView()
        header.customInit(title: "Recent Activities", section: section, delegate: self)

        let tap = UITapGestureRecognizer(target: self, action: #selector(btnFilterPressed(_:)))
        header.btnFilter.addGestureRecognizer(tap)
        if arrSelectedProj != nil || indexSelectedSort != nil {
            header.lblFilterShow.backgroundColor = UIColor(cgColor: cgCForGradientStart)
        }
        return header
    }
    
    deinit {
        print("ActivityViewController Deinitialised")
    }

    func tableView(_ tableView: UITableView, didEndDisplaying cell: UITableViewCell, forRowAt indexPath: IndexPath) {
        if let index = indexForTimer {
            if index == indexPath {
                indexForTimer = nil
            }
        }
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "userBreakInfoCell",
                for: indexPath) as! UserTaskInfoCell
//        let dictValues = taskUpdater.getAllData(taskId: indexPath.row + 1)
        print("Mes \(indexPath)")
        let dictValues = arrDictTaskDetails[indexPath.row]
        cell.lblCategory.backgroundColor = UIColor(cgColor: cgCForGradientStart)
        cell.contentView.backgroundColor = .clear
        if dictValues["Work Process"] as! Bool == true {
            taskUpdater.updateUserTaskTime()
            if let arrProj = arrSelectedProj {
                arrDictTaskDetails = taskUpdater.getTaskDetailsFromProjectName(arrProj: arrProj)
            }
            else {
                let arrProj = projectUpdater.getAllProjectNames()
                arrDictTaskDetails = taskUpdater.getTaskDetailsFromProjectName(arrProj: arrProj)
            }
            arrDictTaskDetails.sort { (task1, task2) -> Bool in
                return (task1[strSortType] as! Int) > (task2[strSortType] as! Int)
            }
            print("fdsfsf\(indexPath)")
            indexForTimer = indexPath
            strTimeLabelCell = dictValues["Total Time"] as! Int
//            nSelectedId = indexPath.row
//            cell.lblCategory.backgroundColor = .greenrgb(140, 20, 252)
            cell.contentView.backgroundColor = UIColor(red: 140/255, green: 20/255, blue: 252/255,
                                                       alpha: 0.2)
        }
        
        cell.lblTotalDuration.text =
            "\(getSecondsToHoursMinutesSeconds(seconds: dictValues["Total Time"] as! Int))"
        let strTime = getSecondsToHoursMinutesSeconds(seconds: dictValues["Start Time"] as! Int)
        if let strDate = dictValues["Start Date"] {
            cell.lblStartTime.text =
                "\(getDateDay(date: strDate as! String)) \(convert24to12Format(strTime: strTime))"
        }
        else {
            cell.lblStartTime.text = "Not Started"
        }
        cell.lblTaskDescription.text = "\(dictValues["Task Name"]!)"
        let projId = dictValues["Project Id"]! as! Int
        cell.lblProjectName.text = "\(projectUpdater.getProjectName(projId: projId))"
        cell.nTaskId = indexPath.row + 1
        print("slhfdsfsfsddshfgsfs\(cell.lblProjectName.numberOfLines)")
        downloadImage(from: projectUpdater.getProjectIconUrl(projectId: projId),
                      imgView: cell.imgVProjectIcon)
        cell.selectionStyle = .none
        
        return cell
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
//        self.indexSeleted = IndexPath(row: 0, section: arrSelectedProj.count - indexPath.row - 1)
        let dictValue = arrDictTaskDetails[indexPath.row]
        let taskId = dictValue["Task Id"] as! Int
        nSelectedId = taskId
        performSegue(withIdentifier: "SegueToTaskController", sender: self)
        timer?.invalidate()
    }

    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
        let dictValue = arrDictTaskDetails[indexPath.row]
        let taskName = dictValue["Task Name"] as! String
        
        let label = UILabel(frame: CGRect(x: 0, y: 0, width: UIScreen.main.bounds.width - 57, height: 20))
        label.numberOfLines = 0
        label.lineBreakMode = .byWordWrapping
        label.text = taskName
        label.sizeToFit()
        
        print("soydfutus \(label.frame)")
        
        return 94 + label.bounds.height
    }

    func runTime() {
        //To start timer object
        timer = Timer.scheduledTimer(timeInterval: 1, target: self, selector:
            #selector(timerAction), userInfo: nil, repeats: true)
        RunLoop.current.add(self.timer!, forMode: RunLoop.Mode.common)
    }
    
    @IBAction func viewPressed(_ sender: Any) {
//        self.viewProfile.isHidden = true
        self.bIsProfileSlideOut = false
        self.nsLProfileViewWidth.constant = -240
        UIView.animate(withDuration: 0.2) {
            self.view.layoutIfNeeded()
        }
    }
    @IBAction func btnAddTaskPressed(_ sender: Any) {
        nSelectedId = nil
        performSegue(withIdentifier: "SegueToTaskController", sender: nil)
//        performSegue(withIdentifier: "ToTaskController", sender: nil)
        timer?.invalidate()
    }
    
    @objc func timerAction() {
        //Update counter label.
        self.strTimeLabel += 1
        self.lblTimer.text = "\(getSecondsToHoursMinutesSeconds(seconds: self.strTimeLabel))"
        self.lblTimer2.text = "\(getSecondsToHoursMinutesSeconds(seconds: self.strTimeLabel))"
        
        if let index = self.indexForTimer {
            let cell = self.tblUserDetails.cellForRow(at: index) as! UserTaskInfoCell
            self.strTimeLabelCell += 1
            cell.lblTotalDuration.text =
            "\(getSecondsToHoursMinutesSeconds(seconds: self.strTimeLabelCell))"
        }
    }
    
    
    
    @IBAction func btnProfileLogoPressed(_ sender: Any) {
        if self.nsLProfileViewWidth.constant == -240 {
            self.bIsProfileSlideOut = true
            self.nsLProfileViewWidth.constant = 0
            UIView.animate(withDuration: 0.2) {
                self.view.layoutIfNeeded()
            }
        }
    }
    
    @IBAction func btnActivitiesPressed(_ sender: Any) {
        performSegue(withIdentifier: "SegueToMyActivity", sender: nil)
        nsLProfileViewWidth.constant = -240
        self.view.layoutIfNeeded()
        //        performSegue(withIdentifier: "ToTaskController", sender: nil)
        timer?.invalidate()
    }
    
    @IBAction func btnAboutPressed(_ sender: Any) {
        let container = NSPersistentContainer(name: "UserTaskDetails")
        print(container.persistentStoreDescriptions.first?.url as Any)
    }
    
    
    
    @IBAction func btnStatePressed(_ sender: Any) {
        //Button action when user starts and ends break.
        if btnState.currentTitle == "Stop" {
            timer!.invalidate()
            userActUpdater.userStartsBreak()
            taskUpdater.userStartsBreak()
            btnState.setTitle("Start", for: .normal)
            btnState.drawPlay(layerHeight: btnState.bounds.width * 0.3)
        }
        else {
            runTime()
            userActUpdater.userFinishedBreak()
            taskUpdater.userFinishedBreak()
            btnState.setTitle("Stop", for: .normal)
            btnState.drawStop(layerHeight: btnState.bounds.width * 0.25)
        }
        userActUpdater.fetchAllData()
    }
    
    @IBAction func btnLogoutPressed(_ sender: Any) {
        //User wants log out.
        if !userActUpdater.IsUserInBreak(){
            showLogoutAlert()
        }
        else {
            showAlertInBreak()
        }
    }
    
    func showAlertInBreak() {
        //If user tries to logout ihn the break time shows alert.
        let alert = UIAlertController(title: "Alert..!", message:
            "Your in a break. Do you want continue?", preferredStyle: UIAlertController.Style.alert)
        alert.addAction(UIAlertAction(title: "Cancel", style: UIAlertAction.Style.default,
                                      handler: { _ in
            
        }))
        alert.addAction(UIAlertAction(title: "Continue", style: UIAlertAction.Style.default,
                                      handler: { _ in
            self.showLogoutAlert()
        }))
        self.present(alert, animated: true, completion: nil)
    }
    
    
    func showLogoutAlert() {
        //Alert while log out.
        let alert = UIAlertController(title: "Log out?", message: "Please select an option",
                                      preferredStyle: UIAlertController.Style.alert)
        alert.addAction(UIAlertAction(title: "Log out",
                                      style: UIAlertAction.Style.default,
                                      handler: {(_: UIAlertAction!) in
                                        //Sign out action
                                        UserDefaults.standard.setValue(false, forKey: "userLogin")
                                        self.timer?.invalidate()
                                        self.taskUpdater.deleteAllData()
                                        self.userActUpdater.deleteAllData()
                                        UserDefaults.standard.removeObject(forKey: "Username")
                                        UserDefaults.standard.removeObject(forKey: "Email")
                                        NotificationCenter.default.removeObserver(self)
                                        self.view.layer.sublayers?.removeAll()
                                        if let viewLogin = self.presentingViewController as? LoginController {
                                            viewLogin.nSleepTime = 0
                                        }
                                        self.presentingViewController?.dismiss(animated: true,
                                                                               completion:
                                        {
                                                self.view = nil
                                                //change.
                                        })
        }))
        alert.addAction(UIAlertAction(title: "Finished Work",
                                      style: UIAlertAction.Style.default,
                                      handler: {(_: UIAlertAction!) in
                                        //Office Leave action
                                        self.userActUpdater.userLoggedOut()
                                        self.taskUpdater.userFinishedWork()
                                        UserDefaults.standard.setValue(false, forKey: "userLogin")
                                        self.timer?.invalidate()
                                        NotificationCenter.default.removeObserver(self)
                                        if let viewLogin = self.presentingViewController as? LoginController {
                                            viewLogin.nSleepTime = 0
                                        }
                                        self.presentingViewController?.dismiss(animated: true,
                                                                               completion:
                                        {
                                            self.view = nil
                                        })
        }))
        alert.addAction(UIAlertAction(title: "Cancel", style: UIAlertAction.Style.default, handler:
            { _ in
            //Cancel Action
        }))
        self.present(alert, animated: true, completion: nil)
    }
}

extension UIView {
    //To add gradient to the view.
    func addGradient(startColor: CGColor, endColor: CGColor, cgPStart: CGPoint = CGPoint(x: 0, y: 0),
                     cgPEnd: CGPoint = CGPoint(x: 1, y: 1), cgFRadius: CGFloat = 0) {
        let gradient = CAGradientLayer()
        print(cgPEnd)
        gradient.frame = CGRect(x: 0, y: 0,
                                width: bounds.width, height: cgPEnd.y * bounds.height)
//        gradient.frame = self.bounds
        gradient.cornerRadius = cgFRadius
        gradient.colors = [startColor, endColor]
        gradient.startPoint = cgPStart
        gradient.endPoint = cgPEnd
        layer.insertSublayer(gradient, at: 0)
    }
}

func getDate() -> String {
    //Function will returns the present date in string type
    let date = Date()
    let calendar = Calendar.current
    let components = calendar.dateComponents([.year, .month, .day, .hour, .minute, .second],
                                             from: date)
    var strDate: String!
    var strMonth: String!
    if components.day! < 10 {
        strDate = "0\(components.day!)"
    }
    else {
        strDate = "\(components.day!)"
    }
    if components.month! < 10 {
        strMonth = "0\(components.month!)"
    }
    else {
        strMonth = "\(components.month!)"
    }
    return "\(strDate!)/\(strMonth!)/\(components.year!)"
}

func getWeekNumber(strDate: String) -> Int {
    let dateFormatter = DateFormatter()
    dateFormatter.dateFormat = "dd/MM/yyyy" //Your date format
    dateFormatter.timeZone = TimeZone(abbreviation: "GMT+0:00") //Current time zone
    //according to date format your date string
    guard let date = dateFormatter.date(from: strDate) else {
        fatalError()
    }
    let calendar = Calendar.current
    let weekOfYear = calendar.component(.weekOfYear, from: date)
    return weekOfYear
}

func getMonthName(strDate: String) -> String {
    let strDate = strDate
    let words = strDate.split(separator: "/")
    let nMonth = Int(words[1])
    let monthStr = Calendar.current.monthSymbols[nMonth! - 1]
    let start = String.Index(utf16Offset: 0, in: monthStr)
    let end = String.Index(utf16Offset: 3, in: monthStr)
    let strMon = String(monthStr[start..<end])
    return strMon
}

func getTime() -> String {
    //Function will returns the present date in string type
    let date = Date()
    let calendar = Calendar.current
    let components = calendar.dateComponents([.hour, .minute, .second], from: date)
    return "\(components.hour!):\(components.minute!):\(components.second!)"
}

func getTimeInSec() -> Int {
    //Returns present time in seconds
    let date = Date()
    let calendar = Calendar.current
    let components = calendar.dateComponents([.hour, .minute, .second], from: date)
    let nTimeSec = components.hour! * 3600 + components.minute! * 60 + components.second!
    return nTimeSec
}

func getSecondsToHoursMinutesSeconds (seconds : Int) -> String {
    //Converts seconds to hh:mm:ss format
    var second = String()
    var minute = String()
    var hour = String()
    let (hr, min, sec) = (seconds / 3600, (seconds % 3600) / 60, (seconds % 3600) % 60)
    if hr > 0 {
        hour = "\(hr):"
    }
    if min == 0 {
        minute = "00:"
    }
    else if min >= 10 {
        minute = "\(min):"
    }
    else {
        minute = "0\(min):"
    }
    
    if sec < 10 {
        second = "0\(sec)"
    }
    else {
        second = "\(sec)"
    }
    return "\(hour)\(minute)\(second)"
}

func getDateDay(date: String) -> String {
    if date == getDate() {
        return "Today"
    }
    else {
        let day = date.split(separator: "/")
        let nDay = Int(day[0])!
        let nMonth = Int(day[1])!
        let nYear = Int(day[2])!
        let currentDay = getDate().split(separator: "/")
        let nCurDay = Int(currentDay[0])!
        let nCurMonth = Int(currentDay[1])!
        let nCurYear = Int(currentDay[2])!
        if nCurDay - nDay == 1 && nMonth == nCurMonth && nYear == nCurYear {
            return "Yesterday"
        }
        else {
            return date
        }
    }
}

func getSecondsToHourMinute (seconds: Int) -> String {
    var minute = String()
    var hour = String()
    let (hr, min) = (seconds / 3600, (seconds % 3600) / 60)
    hour = "\(hr):"
    if min == 0 {
        minute = "00"
    }
    else if min >= 10 {
        minute = "\(min)"
    }
    else {
        minute = "0\(min)"
    }
    return "\(hour)\(minute)"
}

func convert24to12Format(strTime: String) -> String {
    let dateF = DateFormatter()
    dateF.dateFormat = "HH:mm:ss"
    print(strTime)
    let date = dateF.date(from: strTime)
    dateF.dateFormat = "h:mm a"
    return dateF.string(from: date!)
    
}

func convert24to12FormatHourMinute(strTime: String) -> String {
    let dateF = DateFormatter()
    dateF.dateFormat = "HH:mm"
    print(strTime)
    let date = dateF.date(from: strTime)
    dateF.dateFormat = "h:mm a"
    return dateF.string(from: date!)
    
}

func downloadImage(from url: URL, imgView: UIImageView) {
    getData(from: url) { data, response, error in
        guard let data = data, error == nil else { return }
        print(response?.suggestedFilename ?? url.lastPathComponent)
        DispatchQueue.main.async() {
            imgView.image = UIImage(data: data)
        }
    }
}

func getData(from url: URL, completion: @escaping (Data?, URLResponse?, Error?) -> ()) {
    URLSession.shared.dataTask(with: url, completionHandler: completion).resume()
}

extension UIButton {
    func setUpButton() {
        self.addGradient(startColor: cgCForGradientStart, endColor: cgCForGradientEnd,
                         cgFRadius: self.bounds.height / 2)
        self.layer.cornerRadius = self.frame.width / 2
        self.layer.shadowColor = cgCForGradientStart
        self.layer.shadowOffset = CGSize(width: 1.5, height: 1.5)
        self.layer.shadowRadius = 3
        self.layer.shadowOpacity = 0.6
        self.layer.masksToBounds = false
    }
    func drawStop(layerHeight: CGFloat) {
        //To draw a rectangle representing stop condition inside view or button.
        let cgRect = CGRect(x: 0, y: 0, width: layerHeight, height: layerHeight)
        //        if let subLayer = layer.sublayers?[1] {
        //            subLayer.removeFromSuperlayer()
        //        }
        let cgSize = CGSize(width: cgRect.width / 10, height: cgRect.width / 10)
        let cgCPosition = CGPoint(x: frame.width * 0.5 - cgRect.width / 2, y:
            frame.width * 0.5 - cgRect.width / 2)
        
        let cgPRoundRect = UIBezierPath(roundedRect: cgRect, byRoundingCorners:
            .allCorners, cornerRadii: cgSize)
        let shape = CAShapeLayer()
        shape.path = cgPRoundRect.cgPath
        shape.fillColor = UIColor.white.cgColor
        shape.position = cgCPosition
        layer.mask = shape
        if layer.sublayers!.count > 1 {
            print("Here1")
            layer.sublayers!.remove(at: 1)
        }
        layer.insertSublayer(shape, at: 1)
    }
    
    func drawPlay(layerHeight: CGFloat, radius: CGFloat = 2) {
        
        let point1 = CGPoint(x: 0, y: layerHeight)
        let point2 = CGPoint(x: layerHeight, y: layerHeight / 2)
        let point3 = CGPoint(x: 0, y: 0)
        
        let path = CGMutablePath()
        path.move(to: CGPoint(x: 0, y: 0))
        path.addArc(tangent1End: point1, tangent2End: point2, radius: radius)
        path.addArc(tangent1End: point2, tangent2End: point3, radius: radius)
        path.addArc(tangent1End: point3, tangent2End: point1, radius: radius)
        path.closeSubpath()
        
        let uiBez = UIBezierPath(cgPath: path)
        let cgCPosition = CGPoint(x: frame.width * 0.5 - layerHeight / 3,
                                  y: frame.width * 0.5 - layerHeight / 2)
        // Mask to Path
        let shape = CAShapeLayer()
        shape.path = uiBez.cgPath
        shape.fillColor = UIColor.white.cgColor
        shape.position = cgCPosition
        layer.mask = shape
        if layer.sublayers!.count > 1 {
            layer.sublayers!.remove(at: 1)
        }
        layer.insertSublayer(shape, at: 1)
    }
}
extension UITableView {
    func roundCorners(corners: UIRectCorner, radius: CGFloat){
        if #available(iOS 11.0, *) {
            clipsToBounds = true
            layer.cornerRadius = radius
            layer.maskedCorners = CACornerMask(rawValue: corners.rawValue)
        } else {
            layer.cornerRadius = 35
            layer.masksToBounds = true
        }
        
    }
}
