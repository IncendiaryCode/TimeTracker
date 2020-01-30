/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : UserActivityVC.swift
 //
 //    File Created      : 09:Sept:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : User Activity View Controller.
 //
 //////////////////////////////////////////////////////////////////////////// */

import UIKit
import CoreData
import Alamofire

class UserActivityVC: UIViewController, UITableViewDelegate, UITableViewDataSource,
UIGestureRecognizerDelegate, UICollectionViewDelegate, UICollectionViewDataSource, FilterDelegate
, FilterToday {
    /// Button State (Start and Stop)
    @IBOutlet weak var btnState: UIButton!
    /// Label Timer for task and punch
    @IBOutlet weak var tblUserDetails: UITableView!
    @IBOutlet weak var btnAddTask: UIButton!
    @IBOutlet weak var nsLBtnFinishCenter: NSLayoutConstraint!
    @IBOutlet weak var nsLBtnStateCenter: NSLayoutConstraint!
    @IBOutlet weak var btnFinish: UIButton!
    @IBOutlet weak var collectionTimer: UICollectionView!
    @IBOutlet weak var pageCtrlCollection: UIPageControl!
    @IBOutlet weak var nsLCollectionHeight: NSLayoutConstraint!
    @IBOutlet weak var lblPunchInTimer: UILabel!
    @IBOutlet weak var actIndicator: UIActivityIndicatorView!
    
    var arrCTaskDetails: Array<TaskDetails>!
    var viewUserGuideCell: UserguideView!
    var viewUserGuideTask: UserguideView!
    
    /// Refresh table view.
    let refreshControl = UIRefreshControl()
    var maxHeaderHeight: CGFloat! // Maximum Compressing header.
    var minHeaderHeight: CGFloat! // Minimum Compressing header size.

    var timer: Timer? = Timer()
    var nTotalTime: Int!
    var nStopTimerAt: Int!
    var bIsTimerPunch = true // Running timer state (Punch in/out or Task)
    var nSelectedId: Int!
    var bIsFilterHidden = true
    /// Previosly stoped running id.
    var nPrevRunTaskId: Int!
    
    //Filter Data.
    var arrSelectedProj: Array<Int>? // Display selected project, if filter applied.
    var indexSelectedSort: IndexPath! // Selected value for sorting.
    //Enum
    var sortType: SortTypes!    
    var arrIndexOfRunningTask: [IndexPath] = []
    
    var lblEmpty = UILabel() //Label to show empty tasks.
    var btnAddTaskNoData: UIButton! // Button visible when no task available.
    var lblUpdater = UILabel() // Label to indicate first time loader.
    /// Containing running task id.
    var arrRunningTask: Array<Int> = []
    var nSelectedRunTask: Int!
    /// Index path sets in tableview didSelect if punch in not performed.(To run selected task.)
    var indexPathToRun: IndexPath?
    /// dashboard tableview array values applied with only today's task.
    var isTodayShown: Bool = false
    
    // Coredata controller objects.
    var punchInOutCDController: PunchInOutCDController!
    var tasksCDController: TasksCDController!
    var taskTimeCDController: TasksTimeCDController!
    
    override func viewDidLoad() {
        self.navigationController?.interactivePopGestureRecognizer?.delegate = self
        self.navigationController?.interactivePopGestureRecognizer?.isEnabled = true
        
        // Create label to indicate no task.
        lblEmpty.text =  "No task added today!"
        lblEmpty.isHidden = true
        let screenWidth = UIScreen.main.bounds.width
        let screenHeight = UIScreen.main.bounds.height
        lblEmpty.frame = CGRect(x: 0, y: (0.6 * screenHeight), width: screenWidth, height: 30)
        lblEmpty.textAlignment = .center
        view.addSubview(lblEmpty)
        
        // Set no task available add task button.
        let cgRect = CGRect(x: 0, y: 0, width: 100, height: 44)
        btnAddTaskNoData = UIButton(frame: cgRect)
        btnAddTaskNoData.center = CGPoint(x: lblEmpty.center.x, y: lblEmpty.center.y+40)
        btnAddTaskNoData.isHidden = true
        btnAddTaskNoData.setTitle("Add Task", for: .normal)
        btnAddTaskNoData.addTarget(self, action: #selector(btnAddNoTaskPressed(sender:))
            , for: .touchUpInside)
        btnAddTaskNoData.setTitleColor(g_colorMode.textColor(), for: .normal)
        view.addSubview(btnAddTaskNoData)
        
        // Compress header minimum height.
        minHeaderHeight = 105   // Compressing minimum height while scrolling table view.
        maxHeaderHeight = nsLCollectionHeight.constant // Compressing max height while scrolling table view.
        
        tblUserDetails.delegate = self
        tblUserDetails.dataSource = self
        tblUserDetails.clipsToBounds = true
        tblUserDetails.layer.masksToBounds = false
        
        //Register Nib.
        tblUserDetails.register(UINib(nibName: "userBreakInfoCell", bundle: nil),
                                forCellReuseIdentifier: "userBreakInfoCell")
        tblUserDetails.scrollIndicatorInsets = UIEdgeInsets(top: 80, left: 0, bottom: 0, right: 0)
                
        // Add Refresh Control to Table View
        tblUserDetails.addSubview(refreshControl)
        refreshControl.bounds = CGRect(x: refreshControl.bounds.origin.x,
                                       y: -30,
                                       width: refreshControl.bounds.size.width,
                                       height: refreshControl.bounds.size.height)
        
        refreshControl.addTarget(self, action: #selector(refreshTableviewData(_:)), for:
            .valueChanged)
        
        var attributes = [NSAttributedString.Key: AnyObject]()
        attributes[.foregroundColor] = g_colorMode.midColor()
        refreshControl.attributedTitle = NSAttributedString(string: "Fetching Data...",
                                                            attributes: attributes)
        
        arrCTaskDetails = Array()
        
        // Initialise Core data controller objects.
        punchInOutCDController = PunchInOutCDController()
        tasksCDController = TasksCDController()
        taskTimeCDController = TasksTimeCDController()
        
        // Initially set cell sorting order based task id.
        sortType = SortTypes.tasks
        
        //Notifier: when app comes to foreground.
        NotificationCenter.default.addObserver(self, selector: #selector(viewToForeground),
                            name: UIApplication.willEnterForegroundNotification, object: nil)
        
        // Tap gesture to punch in label.
        let tap = UITapGestureRecognizer(target: self, action: #selector(lblPunchInPressed))
        lblPunchInTimer.isUserInteractionEnabled = true
        lblPunchInTimer.addGestureRecognizer(tap)
        self.collectionviewSetup()
        self.updateViewsAndColor()
        
        // Setup label for updating information.
        lblUpdater = UILabel(frame: CGRect(x: 0, y: tblUserDetails.frame.midY
            , width: UIScreen.main.bounds.width, height: 30))
        lblUpdater.textAlignment = .center
        lblUpdater.textColor = .gray
        lblUpdater.text = "We are gettting your task list"
        view.addSubview(lblUpdater)
        actIndicator.startAnimating()
            
        // Update data and view.
        updateProjectDetails()
        updateTaskDetails(pageNo: g_taskPageNo)
        updateView()
        
        // Check for internet connectivity.
        if RequestController.shared.reachabilityManager!.isReachable {
            let punchInOutCDTrlr = PunchInOutCDController()
            
            // Check previous day's punch out time.()
            if punchInOutCDTrlr.isPreviousDayUpdated() {
                // If view initialise from login credentials.
                if nil == arrCTaskDetails || 0 == arrCTaskDetails.count {
                    lblEmpty.isHidden = true
                    btnAddTaskNoData.isHidden = true
                    lblUpdater.isHidden = false
                    actIndicator.startAnimating()
                }
                updateProject()
                // If puched out.
                if punchInOutCDTrlr.isTodayPunchedOut() {
                    btnAddTask.isHidden = true
                }
                collectionTimer.reloadData()
            }
            else {
                // If prev day not updated.
                g_isPunchedOut = false
                g_isPunchedIn = false
                
                let date = punchInOutCDTrlr.getPreviousDate()
                let viewTimeAdder: TimeUpdateView = TimeUpdateView(frame: self.view.bounds)
                
                // Check this issue. time conversion
                viewTimeAdder.customInit(date: date.getStrDate(), time: "09:00:00"
                    , taskName: "", taskId: 0, type: .logout)
                viewTimeAdder.completionHandler = {
                    self.updateProject()
                    self.collectionTimer.reloadData()
                }
                tabBarController?.view.addSubview(viewTimeAdder)
            }
            
            // If punched in not proccessed.
            if !punchInOutCDTrlr.isTodayDateExists() {
                btnAddTask.isHidden = true
            }
        }
        else {
            // Send notification to connect internet.
            let viewNotif = InAppNotificationView()
            viewNotif.sendNotification(msg: "Please connect to internet to find new task/s",
                                       autoDismiss: true)
            viewNotif.addGradient()
            self.view.addSubview(viewNotif)
        }
    }
    
    /// Updates all view.
    func updateView() {
        self.tasksCDController.updateUserTaskTime()
        self.sortAndRefreshData()
        self.arrRunningTask = self.getRunningTaskId()
        self.drawTaskState()
        
        if self.arrRunningTask.count>1 {
            // If more than one task running, show right button initially.
            self.nSelectedRunTask = 0
        }
        self.collectionTimer.reloadData()
        self.refreshControl.endRefreshing()
        
        self.splitMergeStateAndFinishBtn()
        self.actIndicator.stopAnimating()
        self.lblUpdater.isHidden = true
        view.backgroundColor = .clear
    }
    
    override func viewDidLayoutSubviews() {
        // Set frame to label updater.
        let cgPoint = CGPoint(x: actIndicator.frame.midX, y: actIndicator.frame.midY + 30)
        let cgSize = CGSize(width: 250, height: 30)
        lblUpdater.frame.size = cgSize
        lblUpdater.center = cgPoint
    }
    
    func updateProject() {
        /// Update values to core data from server.
        APIResponseHandler.loadProjects(completion: { status in
            if status {
                self.updateTask()
            }
            else {
                // If no internet.
                updateProjectDetails()
                updateTaskDetails(pageNo: self.arrCTaskDetails.count/10)
                self.updateView()
            }
        })
    }
    
    /// Updates task details to core data and view.
    func updateTask(requireAll: Bool? = true) {
        if requireAll! {
            let dispatchGroup = DispatchGroup()
            for pageNo in 1...g_taskPageNo {
                dispatchGroup.enter()
                APIResponseHandler.loadTaskDetails(pageNo: pageNo, completion: {
                    status in
                    if status {
                        dispatchGroup.leave()
                    }
                })
            }
            // Refresh view after completion of all loading.
            dispatchGroup.notify(queue: .main) {
                // Check previous day's task pending.
                let arrTaskTimings = self.taskTimeCDController.getPrevDaysPendingTasks()
                for i in 0..<arrTaskTimings.count {
                    let viewTimeAdder: TimeUpdateView = TimeUpdateView(frame: self.view.frame)
                    let taskTimings = arrTaskTimings[i]
                    let taskName = getTaskName(taskId: taskTimings.taskId!)
                    let strTime = getSecondsToHoursMinutesSeconds(seconds: taskTimings.nStartTime)
                    viewTimeAdder.customInit(date: taskTimings.strDate, time: strTime
                        , taskName: taskName, taskId: taskTimings.taskId!
                        , timeId: taskTimings.timeId, type: .task)
                    
                    // If it is a last index update add completion handler.
                    if i == arrTaskTimings.count - 1 {
                        viewTimeAdder.completionHandler = {
                            self.updateProject()
                        }
                    }
                    self.tabBarController?.view.addSubview(viewTimeAdder)
                }
                if arrTaskTimings.count == 0 {
                    self.updateView()
                }
            }
        }
        else {
            APIResponseHandler.loadTaskDetails(pageNo: g_taskPageNo, completion: {
                status in
                if status {
                    self.updateView()
                }
            })
        }
    }
    
    func updateViewsAndColor() {
        // Setup initial views.
        self.view.backgroundColor = g_colorMode.defaultColor()
        self.view.addGradient(cgPStart: CGPoint(x: 0, y: 0), cgPEnd: CGPoint(x: 1, y: 0.5))
        tblUserDetails.roundCorners(corners: [.topLeft, .topRight], radius: 35.0)
        btnState.addGradient(cgFRadius: btnState.bounds.height / 2)
        btnFinish.addGradient(cgFRadius: btnState.bounds.height / 2)
        lblEmpty.textColor = .lightGray
        refreshControl.tintColor = g_colorMode.midColor()
        tblUserDetails.backgroundColor = g_colorMode.defaultColor()
        tblUserDetails.layer.borderWidth = 0.3
        tblUserDetails.layer.borderColor = g_colorMode.textColor().cgColor
        tblUserDetails.reloadData()
    }
    
    override func viewDidAppear(_ animated: Bool) {
        super.viewDidAppear(true)
        if arrIndexOfRunningTask.count > 0 {
            // To start animation running cell to top
//            tblUserDetails.reloadRows(at: arrIndexOfRunningTask, with: .none)
        }
        showIntroPage()
    }
    
    /// Show intro page if required.
    func showIntroPage() {
        /// Create intro page if first time installed.(Showing add task)
        if (nil == UserDefaults.standard.object(forKey: "IntroStatusTask") ||
            false == UserDefaults.standard.value(forKey: "IntroStatusTask") as? Bool)
            && g_isPunchedIn ?? false && !g_isPunchedOut {
            let cgRect = CGRect(x: btnAddTask.frame.minX, y: btnAddTask.frame.minY-11
                , width: btnAddTask.frame.width, height: btnAddTask.frame.height)
            let userGuideData = UserguideData(itemFrame: cgRect, itemHint:
                "To create new task.")
            viewUserGuideTask = UserguideView(userguideData: userGuideData)
            viewUserGuideTask.completionHandler = {
                UserDefaults.standard.setValue(true, forKey: "IntroStatusTask")
                if self.arrCTaskDetails.count != 0 {
                    self.setupCellintroPage()
                }
            }
            tabBarController?.view.addSubview(viewUserGuideTask)
        }
    }
    
    /// Provides all currently running task id.
    func getRunningTaskId() -> Array<Int> {
        var array = Array<Int>()
        for cTaskDetails in arrCTaskDetails {
            if true == cTaskDetails.bIsRunning {
                array.append(cTaskDetails.taskId)
            }
        }
        return array
    }
    
    /// Called when table view refreshed.
    @objc func refreshTableviewData(_ sender: Any) {
        // Fetch Data from server.
        fetchTaskDataFromServer()
    }
    
    /// Fetch data from server.
    func fetchTaskDataFromServer() {
        updateProject()
    }
    
    @IBAction func btnFinishPressed(_ sender: Any) {
        
    }
    
    /// Punch In label pressed.
    @objc func lblPunchInPressed(sender: UITapGestureRecognizer) {
        if lblPunchInTimer.text == "Punch in" {
            nTotalTime = punchInOutCDController.getTotalTime()
            nStopTimerAt = nTotalTime + 5 // Stop after 5 sec
            lblPunchInTimer.text =
                "\(getSecondsToHoursMinutesSecondsWithAllFields(seconds: self.nTotalTime))"
            
            // Animate while changing font.
            UIView.animate(withDuration: 0.2, delay: 0.0,
                options: .curveEaseInOut, animations: {
                    self.lblPunchInTimer.transform = CGAffineTransform(scaleX: 2.0, y: 2.0)
                }, completion: { _ in
                    UIView.animate(withDuration: 0.2) {
                        self.lblPunchInTimer.transform = CGAffineTransform(scaleX: 1.0, y: 1.0)
                    }
            })
            runTime()
        }
    }
    
    /// To start timer object
    func runTime() {
        timer = Timer.scheduledTimer(timeInterval: 1, target: self, selector:
            #selector(timerAction), userInfo: nil, repeats: true)
        RunLoop.current.add(self.timer!, forMode: RunLoop.Mode.common)
    }
    
    @objc func timerAction() {
        //Update punch in timer label.
        self.nTotalTime += 1
        if nTotalTime <= nStopTimerAt {
            lblPunchInTimer.text =
                "\(getSecondsToHoursMinutesSecondsWithAllFields(seconds: self.nTotalTime))"
        }
        else {
            lblPunchInTimer.text = "Punch in"
            timer?.invalidate()
        }
    }
    /// Draw play or stop icon in button.
    func drawTaskState() {
        if (arrRunningTask.count == 0 && !punchInOutCDController.isTodayDateExists())
            || true == g_isPunchedOut {
            btnState.setTitle("Start", for: .normal)
            btnState.drawPlay(layerHeight: btnState.bounds.width * 0.30)
        }
        else {
            btnState.setTitle("Stop", for: .normal)
            btnState.drawStop(layerHeight: btnState.bounds.width * 0.25)
        }
        splitMergeStateAndFinishBtn()
    }
    
    func gestureRecognizerShouldBegin(_ gestureRecognizer: UIGestureRecognizer) -> Bool {
        // To enable swipe navigation.
        if gestureRecognizer == self.navigationController?.interactivePopGestureRecognizer {
            return false
        }
        return true
    }
    
    /// Sorts tableview cell based on strSortType and reloads table view
    func sortAndRefreshData() {
        // If filter applied.
        if let arrProj = arrSelectedProj {
            arrCTaskDetails = tasksCDController
                .getTaskDetailsFromProjectNameUnFinished(arrProj: arrProj, onlyTodays: isTodayShown)
        }
        else {
            let arrProj = getAllProjectIds()
            arrCTaskDetails = tasksCDController
                .getTaskDetailsFromProjectNameUnFinished(arrProj: arrProj, onlyTodays: isTodayShown)
        }
        let arrProj = getAllProjectIds()
        g_arrCTaskDetails = tasksCDController
            .getTaskDetailsFromProjectNameUnFinished(arrProj: arrProj)
        var nContainsOfflineTask: Int?
        // Sort array of task based on sort type.
        var i = 0
        arrCTaskDetails.sort { (task1, task2) -> Bool in
            switch (sortType) {
                case .tasks :
                    if task1.taskId < 0 {
                        // Set contains offline task in array.
                        if nil == nContainsOfflineTask {
                            nContainsOfflineTask = i+1
                        }
                        if task2.taskId < 0 {
                            return task1.taskId < task2.taskId
                        }
                    }
                    i += 1
                    return task1.taskId > task2.taskId
                case .projects:
                    return task1.projId > task2.projId
                case .duration:
                    return task1.nTotalTime > task2.nTotalTime
                default:
                    return false
            }
        }
        
        // Check for offline tasks. (Sort local_task_ids to top, ids are stored in negative value).
        if let indexShift = nContainsOfflineTask {
            arrCTaskDetails = arrCTaskDetails.shift(withDistance:
                indexShift - arrCTaskDetails.count)
        }
        
        if arrCTaskDetails.count == 0 {
            lblEmpty.isHidden = false
            btnAddTaskNoData.isHidden = false
        }
        tblUserDetails.reloadData()
    }
    
    /// Sorts table view based on Sort Type.
    func updateArrayDetails() {
        // If filter applied.
        if let arrProj = arrSelectedProj {
            arrCTaskDetails = tasksCDController
                .getTaskDetailsFromProjectNameUnFinished(arrProj: arrProj, onlyTodays: isTodayShown)
        }
        else {
            let arrProj = getAllProjectIds()
            arrCTaskDetails = tasksCDController
                .getTaskDetailsFromProjectNameUnFinished(arrProj: arrProj, onlyTodays: isTodayShown)
        }
        
        var nContainsOfflineTask: Int?
        // Sort array of task based on sort type.
        var i = 0
        arrCTaskDetails.sort { (task1, task2) -> Bool in
            switch (sortType) {
                case .tasks :
                    if task1.taskId < 0 {
                        // Set contains offline task in array.
                        if nil == nContainsOfflineTask {
                            nContainsOfflineTask = i+1
                        }
                        if task2.taskId < 0 {
                            return task1.taskId < task2.taskId
                        }
                    }
                    i += 1
                    return task1.taskId > task2.taskId
                case .projects:
                    return task1.projId > task2.projId
                case .duration:
                    return task1.nTotalTime > task2.nTotalTime
                default:
                    return false
            }
        }
        
        // Check for offline tasks. (Sort local_task_ids to top, ids are stored in negative value).
        if let indexShift = nContainsOfflineTask {
            arrCTaskDetails = arrCTaskDetails.shift(withDistance:
                indexShift - arrCTaskDetails.count)
        }
        
        if arrCTaskDetails.count == 0 {
            lblEmpty.isHidden = false
            btnAddTaskNoData.isHidden = false
        }
    }
    
    @objc func btnAddNoTaskPressed(sender: UIButton!) {
        if !(g_isPunchedIn ?? false) {
            alertToPuchIn()
        }
        else if g_isPunchedOut {
            showAlert(msg: "You cannot add task once you have punched out!")
        }
        else {
            segueToAddTask()
        }
    }
    
    @objc private func viewToForeground() {
        // Check date of opening app from background.
        sortAndRefreshData()
        drawTaskState()
        collectionTimer.reloadData()
    }
    
    func setupCellintroPage() {
        /// Create intro page if first time installed.(Showing cell)
        if nil == UserDefaults.standard.object(forKey: "IntroStatusCell") ||
            false == UserDefaults.standard.value(forKey: "IntroStatusCell") as? Bool {
            tblUserDetails.scrollToRow(at: [0,0], at: .top, animated: false)
            let cell = tblUserDetails.cellForRow(at: [0,0])!
            
            let cgRect = CGRect(x: cell.frame.minX, y: cell.frame.minY+tblUserDetails.frame.minY
                , width: cell.frame.width, height: cell.frame.height)
            let userGuideData = UserguideData(itemFrame: cgRect, itemHint:
                "1. Tap to start/stop the task.\n2. Swipe left for edit/stop.")
            viewUserGuideCell = UserguideView(userguideData: userGuideData)
            viewUserGuideTask.completionHandler = {
                UserDefaults.standard.setValue(true, forKey: "IntroStatusCell")
            }
            tabBarController?.view.addSubview(viewUserGuideCell)
        }
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        if arrCTaskDetails.count > 0 {
            lblEmpty.isHidden = true
            btnAddTaskNoData.isHidden = true
            return arrCTaskDetails.count
        }
        else {
            return 0
        }
    }
    
    func numberOfSections(in tableView: UITableView) -> Int {
        return 1
    }
    
    func tableView(_ tableView: UITableView, heightForHeaderInSection section: Int) -> CGFloat {
            return 110
    }
    
    func tableView(_ tableView: UITableView, heightForFooterInSection section: Int) -> CGFloat {
        return 90
    }
    
    func tableView(_ tableView: UITableView, didHighlightRowAt indexPath: IndexPath) {
        if let cell = tableView.cellForRow(at: indexPath) as? UserTaskInfoCell {
            // Highlight touch.
//            cell.alpha = 0.5
            UIView.animate(withDuration: 0.2, animations: {
//                cell.contentView.layer.borderColor = UIColor.lightGray.withAlphaComponent(0.2).cgColor
                cell.center = CGPoint(x: cell.center.x+3, y: cell.center.y+3)
            })
        }
    }
    
    func tableView(_ tableView: UITableView, didUnhighlightRowAt indexPath: IndexPath) {
        if let cell = tableView.cellForRow(at: indexPath) as? UserTaskInfoCell {
//            cell.alpha = 1
            UIView.animate(withDuration: 0.2, animations: {
//                cell.contentView.layer.borderColor = UIColor.lightGray.withAlphaComponent(0.5).cgColor
                cell.center = CGPoint(x: cell.center.x-3, y: cell.center.y-3)
            })
        }
    }

    /// Table header button filter pressed
    @objc func btnFilterPressed(_ sender: Any) {
        if bIsFilterHidden {
            // Segue to show filer view.
            bIsFilterHidden = false
            performSegue(withIdentifier: "ShowFilter", sender: nil)
            self.view.alpha = 0.2
        }
    }
    
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        if segue.destination is FilterViewController {
            let vc = segue.destination as? FilterViewController
            vc?.delegate = self
            if let arrProj = arrSelectedProj {
                // If already filter applied pass the filter values to filter view.
                vc?.arrSelectedProj = Array<Int>()
                vc?.arrSelectedProj = arrProj
            }
            if let index = indexSelectedSort {
                // If already sort applied pass the sort type to filter view.
                vc?.indexSelected = index
            }
        }
       else if segue.identifier == "SegueToTaskController" {
            let taskController = segue.destination as! TaskViewController
            taskController.taskId = nSelectedId
            
            // Set activity state button value to task controller.
            if btnState.currentTitle == "Stop" {
                taskController.bStateInPlay = true
            }
            else {
                taskController.bStateInPlay = false
            }
        }
        else if segue.identifier == "SegueToMyActivity" {
            // Update task timings.
            tasksCDController.updateUserTaskTime()
        }
    }
    
    // Delegate function from filer view.
    func selectedProjects(arrProj: Array<Int>) {
        // Display only selected projects.
        bIsFilterHidden = true
        arrSelectedProj = Array<Int>()
        arrSelectedProj = arrProj
        sortAndRefreshData()
        self.view.alpha = 1
    }
    
    // Delegate from filer view.
    func dismissedView() {
        // Dismissed view from filter view.
        bIsFilterHidden = true
        self.view.alpha = 1
    }
    
    // Delegate from filer view.
    func clearedFilter() {
        // Apply clear filter.
        bIsFilterHidden = true
        arrSelectedProj = nil
        indexSelectedSort = nil
        
        // Set sort type to recent tasks.
        sortType = SortTypes.tasks
        sortAndRefreshData()
        self.view.alpha = 1
    }
    
    // Delegate from filer view.
    func sortApplied(sortType: SortTypes, indexSelected: IndexPath) {
        self.sortType = sortType
        indexSelectedSort = indexSelected
        bIsFilterHidden = true
        self.view.alpha = 1
        sortAndRefreshData()
    }
    
    func changedFilterViewPosition(cgFAlpha: CGFloat) {
        if cgFAlpha >= 0.2 {
            self.view.alpha = cgFAlpha
        }
    }
    
    func scrollViewDidScroll(_ scrollView: UIScrollView) {
        /// When scrolled to bottom.
        if scrollView.contentSize.height >= self.tblUserDetails.frame.size.height &&
            scrollView.contentOffset.y >= scrollView.contentSize.height - scrollView.frame
                .size.height {
            // Disable bounce comes to bottom. (Because, it effects animation if less tasks exist)
            scrollView.bounces = false
            return
        }
        scrollView.bounces = true
        if tblUserDetails.contentOffset.y >= 0 {
            hideHeaderView() // Compress header.
        }
    }
    
    func tableView(_ tableView: UITableView, willDisplay cell: UITableViewCell, forRowAt
        indexPath: IndexPath) {
        // Check total taskpage is set.(if no network)
        if nil == g_totalPagesTask {
            g_totalPagesTask = arrCTaskDetails.count/10
        }
        
        // Detect tableview reached to almost bottom (-4).
        if indexPath.row-3 == arrCTaskDetails.count-4 && g_taskPageNo < g_totalPagesTask {
            g_taskPageNo += 1
            updateTask()
        }
    }

    /// Move table view minY position while scrolling.
    func hideHeaderView() {
        guard let tblUserDetails = tblUserDetails else {
            return
        }
        if (tblUserDetails.contentOffset.y + minHeaderHeight) <= maxHeaderHeight {
//            if (nsLCollectionHeight.constant + minHeaderHeight) <= maxHeaderHeight {
            // While scrolling.
            nsLCollectionHeight.constant = maxHeaderHeight - tblUserDetails.contentOffset.y
            let progress = (nsLCollectionHeight.constant - minHeaderHeight)
                / (maxHeaderHeight - minHeaderHeight)
            pageCtrlCollection.alpha = progress
            lblPunchInTimer.alpha = progress
        }
        else {
            // If scrolling velocity greater than threshold value, set constraints without
            // animation.
            nsLCollectionHeight.constant = minHeaderHeight
            lblPunchInTimer.alpha = 0
            pageCtrlCollection.alpha = 0
        }
    }
    
    func tableView(_ tableView: UITableView, viewForHeaderInSection section: Int) -> UIView? {
        let header = TableHeaderView()
        if isTodayShown {
            header.customInit(title: "Today's Activities", section: section)
        }
        else {
            header.customInit(title: "Recent Activities", section: section)
        }
        header.contentView.backgroundColor = g_colorMode.defaultColor()
        header.switchFilter.isHidden = false
        header.lblTitleSwitch.isHidden = false
        header.delegate = self
        header.switchFilter.setOn(isTodayShown, animated: false)
        header.contentView.backgroundColor = g_colorMode.defaultColor()
        // tap gesture to Filter button.
        let tap = UITapGestureRecognizer(target: self, action: #selector(btnFilterPressed(_:)))
        header.btnFilter.addGestureRecognizer(tap)
        if arrSelectedProj != nil || indexSelectedSort != nil {
            // If filter applied show indicator.
            header.lblFilterIndicator.backgroundColor = UIColor(cgColor: g_colorMode.endColor())
        }
        return header
    }
    
    func switchChanged(to value: Bool) {
        isTodayShown = value
        updateArrayDetails()
        tblUserDetails.reloadWithAnimationEaseInOut()
    }
    
    func tableView(_ tableView: UITableView, viewForFooterInSection section: Int) -> UIView? {
        // Only if tasks loaded to core data.
        if nil != g_totalPagesTask && 0 != arrCTaskDetails.count {
            let footerView = UITableViewHeaderFooterView()
            // Setup label.
            var cgPoint = CGPoint(x: tableView.frame.midX, y: footerView.frame.maxY+30)
            var cgSize = CGSize(width: 150, height: 20)
            let label = UILabel(frame: CGRect.zero)
            label.frame.size = cgSize
            label.center = cgPoint
            label.textAlignment = .center
            label.font = label.font.withSize(12)
            label.textColor = .gray
            footerView.addSubview(label)
            
            // Setup image view.
            cgPoint = CGPoint(x: tableView.frame.midX, y: footerView.frame.maxY+10)
            cgSize = CGSize(width: 15, height: 15)
            let imgVLoader = UIImageView(frame: CGRect.zero)
            imgVLoader.frame.size = cgSize
            imgVLoader.center = cgPoint
            footerView.addSubview(imgVLoader)
            if g_taskPageNo < g_totalPagesTask {
                // If all pages not loaded.
                label.text = "Getting your task list"
                imgVLoader.image = #imageLiteral(resourceName: "synch")
            }
            else {
                label.text = "That's all!"
                imgVLoader.image = nil
            }
            return footerView
        }
        else {
            return nil
        }
    }
    
    deinit {
        print("ActivityViewController Deinitialised")
    }
    
    func tableView(_ tableView: UITableView, canEditRowAt indexPath: IndexPath) -> Bool {
        // Without puchin updation no edit option, as well as after punched out.
//        if !(g_isPunchedIn ?? false) || (g_isPunchedOut) {
//            return false
//        }
        return true
    }
    
    func tableView(_ tableView: UITableView, editActionsForRowAt indexPath: IndexPath)
        -> [UITableViewRowAction]? {
        let cTaskDetails = arrCTaskDetails[indexPath.row]
        let taskId = cTaskDetails.taskId
            let editAction = UITableViewRowAction(style: .normal, title: "Edit" , handler: {
                    (action:UITableViewRowAction, indexPath: IndexPath) -> Void in
            self.nSelectedId = taskId
            self.performSegue(withIdentifier: "SegueToTaskController", sender: self)
        })
        
        editAction.backgroundColor = UIColor(cgColor: g_colorMode.endColor())
        
        let stopAction = UITableViewRowAction(style: .default, title: "Stop" , handler: {
            (action:UITableViewRowAction, indexPath: IndexPath) -> Void in
            self.startOrStopTask(indexPath: indexPath)
        })
        
        stopAction.backgroundColor = UIColor(cgColor: g_colorMode.endColor())
        
        if arrRunningTask.contains(taskId!) {
            // If a task running then, send only done action.
            return [stopAction]
        }
        else {
            return [editAction]
        }
    }
    
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "userBreakInfoCell",
                for: indexPath) as! UserTaskInfoCell
        let cTaskDetails = arrCTaskDetails[indexPath.row]
        
        // If punched out.
//        if true == g_isPunchedOut {
//            cell.isUserInteractionEnabled = false
//        }
//        else {
        cell.isUserInteractionEnabled = true
//        }
        
        cell.lblTotalDuration.text =
        "\(getSecondsToHoursMinutesSeconds(seconds: cTaskDetails.nTotalTime!))"
        cell.imgTimer.image = #imageLiteral(resourceName: "timer3")
        cell.imgTimer.alpha = 1
        // If cell equals to running task cell.
        if cTaskDetails.bIsRunning! == true {
            // Store running task id.
            nPrevRunTaskId = cTaskDetails.taskId
            tasksCDController.updateUserTaskTime()
            updateArrayDetails()
            
            if !(arrIndexOfRunningTask.contains(indexPath)) {
                arrIndexOfRunningTask.append(indexPath) // running task indexpath.
            }
            cell.lblTotalDuration.text = "Running"
            cell.imgTimer.image = #imageLiteral(resourceName: "task")
            cell.imgTimer.alpha = 0.5
        }
        
        // If task started.
        if let strDate = cTaskDetails.getStartDate() {
            let strTime = getSecondsToHoursMinutesSecondsWithAllFields(seconds:
                cTaskDetails.getStartTime()!)
            cell.lblStartTime.text =
            "\(getDateDay(date: strDate)) \(convert24to12Format(strTime: strTime))"
        }
        // If task not started.
        else {
            cell.lblStartTime.text = "Not Started"
        }
        
        cell.lblTaskName.text = "\(cTaskDetails.taskName!)"
        let projId = cTaskDetails.projId
        let cProjectDetails = g_dictProjectDetails[projId!]!
        
        cell.lblProjectName.text = "\(cProjectDetails.projName!)"
        cell.lblCategory.backgroundColor = cProjectDetails.color
        
        // Update project icon.
        if let img = g_dictProjectDetails[projId!]?.imgProjIcon {
            cell.imgVProjectIcon.image = img
        }
        else {
            downloadImage(from: g_dictProjectDetails[projId!]!.urlProjIcon,
                          imgView: cell.imgVProjectIcon)
        }
        return cell
    }
    
    /// Start task.
    func startTask(taskId: Int) {
        tasksCDController.startTask(taskId: taskId, completion: {
            status in
            if status {
                print("Started successfully.!")
            }
            else {
                print("Error while starting task..!")
                self.actIndicator.stopAnimating()
            }
            self.updateProject()
        })
    }
    
    /// To stop task.
    func stopTask(taskId: Int, completion: @escaping (() -> Void) = {}) {
        tasksCDController.stopTask(taskId: taskId, completion: {
            status in
            if status {
                // If task stops and selected timer collection cell also same.
//                if self.nSelectedRunTask == self.arrRunningTask.count - 1 {
//                    self.nSelectedRunTask -= 1
//                }
                print("Stoped successfully.!")
            }
            else {
                print("Error while starting task..!")
            }
            self.updateProject()
            completion()
        })
    }
    
    func alertToPuchIn() {
        //Alert while puch in.
        let alert = UIAlertController(title: "Punch In", message: "Do you want to punch in for the day?",
                                      preferredStyle: UIAlertController.Style.alert)
        alert.addAction(UIAlertAction(title: "Yes", style: UIAlertAction.Style.default,
                                      handler: {(_: UIAlertAction!) in
                                        self.updatePunchIn()
        }
        ))
        alert.addAction(UIAlertAction(title: "No", style: UIAlertAction.Style.cancel, handler:
            { _ in
                //Cancel Action
        }))
        self.present(alert, animated: true, completion: nil)
    }
    
    // Reload collection view.
    func reloadCollection() {
        self.arrRunningTask = self.getRunningTaskId()
        self.drawTaskState()
        if self.arrRunningTask.count>1 {
            // If more than one task running, show right button initially.
            self.nSelectedRunTask = 0
        }
        self.collectionTimer.reloadData()
    }
    
    /// To start or stop task.
    func startOrStopTask(indexPath: IndexPath?) {
        guard g_isPunchedIn ?? false else {
            alertToPuchIn()
            return
        }
        
        if let indxPath = indexPath {
            animateCellPosition(indexPath: indxPath)
            let cell = tblUserDetails.cellForRow(at: indxPath) as! UserTaskInfoCell
            let cTaskDetails = arrCTaskDetails[indxPath.row]
            let taskId = cTaskDetails.taskId // Fetch task id from selected cell
            
            if cTaskDetails.bIsRunning! {
                // Stop running task.
                cTaskDetails.bIsRunning = false
                cell.lblTotalDuration.text = "Stoping"
                reloadCollection()
                stopTask(taskId: taskId!)
            }
            else {
                // Check for already time exists.
                guard !taskTimeCDController.isCurrentOrFutureTimeExist(taskId: taskId!) else {
                    tblUserDetails.reloadData()
                    showAlert(msg:
    "This task already containing present/future time. Please delete those timings before start.")
                    return
                }
                
                // Start task.
                cTaskDetails.bIsRunning = true
                cell.lblTotalDuration.text = "Synching"
                reloadCollection()
                startTask(taskId: taskId!)
            }
            cell.imgTimer.image = #imageLiteral(resourceName: "synch")
            cell.isUserInteractionEnabled = false
        }
        else {
            //Button action when user starts and ends break.
            if btnState.currentTitle == "Stop" && arrRunningTask.count > 0 {
                let index = pageCtrlCollection.currentPage
                let taskId = arrRunningTask[index]
                let indexArray = arrCTaskDetails.firstIndex(where: {
                    (item) -> Bool in
                    item.taskId == taskId
                })!
                
                let indexPath = IndexPath(row: indexArray, section: 0)
                let cTaskDetails = arrCTaskDetails[indexPath.row]
                let cell = tblUserDetails.cellForRow(at: indexPath) as! UserTaskInfoCell
                animateCellPosition(indexPath: indexPath)

                // Stop running task.
                cell.isUserInteractionEnabled = false
                cell.imgTimer.image = #imageLiteral(resourceName: "synch")
                cTaskDetails.bIsRunning = false
                cell.lblTotalDuration.text = "Stoping"
                reloadCollection()
                stopTask(taskId: taskId)
            }
            else {
                // Provide alert message when there is no task id top start task.
                showPunchOutAlert()
            }
        }
    }
    
    /// When action performed to cell animate cell.
    func animateCellPosition(indexPath: IndexPath) {
        if let cell = tblUserDetails.cellForRow(at: indexPath) as? UserTaskInfoCell {
            // Highlight touch.
//            cell.contentView.layer.borderColor = UIColor.lightGray.cgColor
            UIView.animate(withDuration: 0.1, animations: {
                cell.center = CGPoint(x: cell.center.x+3, y: cell.center.y+3)
            }, completion: {
                _ in
                // Remove animated style.
//                cell.contentView.layer.borderColor = UIColor.lightGray.withAlphaComponent(0.5)
//                    .cgColor
                UIView.animate(withDuration: 0.1, animations: {
                    cell.center = CGPoint(x: cell.center.x-3, y: cell.center.y-3)
                })
            })
        }
    }
    
    /// Alert to punch out
    func showPunchOutAlert() {
        //Alert while log out.
        let alert = UIAlertController(title: "Punch out"
            , message: "Are you sure want to punch out for the day?",
            preferredStyle: UIAlertController.Style.alert)
        alert.addAction(UIAlertAction(title: "Yes", style: UIAlertAction.Style.default,
                                      handler: {(_: UIAlertAction!) in
                                        //Office Leave action
                                        self.btnState.isUserInteractionEnabled = false
                                        self.updatePunchOutTime()
        }
        ))
        alert.addAction(UIAlertAction(title: "No", style: UIAlertAction.Style.cancel, handler:
            { _ in
                //Cancel Action
        }))
        self.present(alert, animated: true, completion: nil)
    }
    
    /// Update punch out timings.
    func updatePunchOutTime() {
        actIndicator.startAnimating()
        APIResponseHandler.stopTaskOrPunchIn(completion: {
            status in
            if status {
                print("Punch out time updated..!")
                g_isPunchedOut = true
                APIResponseHandler.loadPunchInOut(pageNo: 1, completion: {
                    status in
                    if status {
                        let viewNotif = InAppNotificationView()
                        viewNotif.sendNotification(msg: "Punched out successfully",
                                                   autoDismiss: true)
                        viewNotif.addGradient()
                        self.view.addSubview(viewNotif)
                        self.collectionTimer.reloadData()
                        self.tblUserDetails.reloadData()
                        self.btnState.drawPlay(layerHeight: self.btnState.bounds.width * 0.30)
                        self.btnAddTask.isHidden = true
                    }
                    else {
                        print("Error while updating punch out time..!")
                    }
                    self.btnState.isUserInteractionEnabled = true
                })
            }
            else {
                print("Error while updating punch out time..!")
            }
            self.actIndicator.stopAnimating()
        })
    }
    
    /// Stop all task while starting new task, if multi task disabled.
    func stopRunningTask(stopAll: Bool = true, completion: @escaping () -> ()) {
        // Stop running task.
        func stop(taskId: Int) {
            let indexTable = arrCTaskDetails.firstIndex(where: {
                return $0.taskId == taskId
            })
            let indexPath = IndexPath(row: indexTable!, section: 0)
            let cell = tblUserDetails.cellForRow(at: indexPath)! as! UserTaskInfoCell
            let cTaskDetails = arrCTaskDetails[indexPath.row]
            cell.imgTimer.image = #imageLiteral(resourceName: "synch")
            cell.isUserInteractionEnabled = false
            cTaskDetails.bIsRunning = false
            cell.lblTotalDuration.text = "Stoping"
            reloadCollection()
            tasksCDController.stopTask(taskId: taskId, completion: {
                status in
                if status {
                    print("Successfully stoped")
                }
                else {
                    print("Error while stoping task")
                }
            })
        }
        
        if stopAll {
            // All running tasks.
            for taskId in arrRunningTask {
                stop(taskId: taskId)
            }
        }
        else {
            // Except one running tasks.
            if arrRunningTask.count != 0 {
                for i in 0..<arrRunningTask.count-1 {
                    let taskId = arrRunningTask[i]
                    stop(taskId: taskId)
                }
            }
        }
        completion()
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        // Check punched out.
        if g_isPunchedOut {
            showAlert(
                msg: "Sorry, your already punched out. Please login tommorow to start a task.")
            return
        }
        
        // If not punched in.
        if !(g_isPunchedIn ?? false)  {
            indexPathToRun = indexPath
        }
        
        // Check for multi task disabled.
        if false == UserDefaults.standard.object(forKey: "multi_task") as? Bool {
            let taskId = arrCTaskDetails[indexPath.row].taskId
            // Check for running task atleast one and tap not on running task.
            if arrRunningTask.count > 0 && arrRunningTask[0] != taskId {
                stopRunningTask() {
                    // completion handler.
                    self.startOrStopTask(indexPath: indexPath)
                }
            }
            else {
                self.startOrStopTask(indexPath: indexPath)
            }
        }
        else {
            startOrStopTask(indexPath: indexPath)
        }
    }

    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
//        let cTaskDetails = g_arrCTaskDetails[indexPath.row]
//        let taskName = cTaskDetails.taskName
        
        // Create new label, to determine the cell height. Incase task name contains more than one
        // line characters increase cell height based on line numbers.
//        let label = UILabel(frame: CGRect(x: 0, y: 0, width: UIScreen.main.bounds.width - 57,
//                                          height: 20))
//        label.numberOfLines = 0
//        label.lineBreakMode = .byWordWrapping
//        label.text = taskName
//        label.sizeToFit()
//        let labelHeight = label.bounds.height
        
//        return 100 + labelHeight
        return 120
    }
    
    @IBAction func btnAddTaskPressed(_ sender: Any) {
        segueToAddTask()
        let container = NSPersistentContainer(name: "UserTaskDetails")
        print(container.persistentStoreDescriptions.first?.url as Any)
    }
    
    func segueToAddTask() {
        nSelectedId = nil // set selected task id to nil.
        performSegue(withIdentifier: "SegueToTaskController", sender: nil)
    }
    
    /// To split and merge state and finish button. When state is playing splits and when stop merges.
    func splitMergeStateAndFinishBtn() {
//        if btnState.titleLabel!.text == "Start" {
//            nsLBtnStateCenter.constant = 0
//            nsLBtnFinishCenter.constant = 0
//            nsLCollectionHeight.constant = 0
//            UIView.animate(withDuration: 0.5) {
//                self.view.layoutIfNeeded()
//            }
//        }
//        else {
//            nsLBtnStateCenter.constant = 50
//            nsLBtnFinishCenter.constant = -50
            
            // Animate table expanding.
//            if nsLCollectionHeight.constant == 0 {
//                nsLCollectionHeight.constant = maxHeaderHeight
//                UIView.animate(withDuration: 0.5) {
//                    self.view.layoutIfNeeded()
//                }
//            }
//        }
    }
    
    /// Alert punched out.
    func showAlert(msg: String) {
        let alert = UIAlertController(title: "Alert"
            , message: msg, preferredStyle: UIAlertController.Style.alert)
        alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default,
                                      handler: {(_: UIAlertAction!) in
        }
        ))
        self.present(alert, animated: true, completion: nil)
    }
    
    @IBAction func btnStatePressed(_ sender: Any) {
        guard g_isPunchedOut != true else {
            showAlert(
                msg: "Sorry, your already punched out. Please login tommorow for update punchin.")
            return
        }
        
        // If not punched in.
        if !(g_isPunchedIn ?? false)  {
            indexPathToRun = nil
        }
        startOrStopTask(indexPath: nil)
    }
    
    /// To update punch in time.
    func updatePunchIn() {
        // Update punch in time.
        let viewTimeAdder: TimeUpdateView = TimeUpdateView(frame: self.view.frame)
        viewTimeAdder.customInit(date: getCurrentDate(), time: getCurrentTime()
            , taskName: "", taskId: 0, type: .login)
        viewTimeAdder.completionHandler = {
            self.updateProject()
            g_isPunchedIn = true
            self.btnAddTask.isHidden = false
            self.collectionTimer.reloadData()
            if let indexPath = self.indexPathToRun {
                self.startOrStopTask(indexPath: indexPath)
            }
            
            // If intro page not shown.
            self.showIntroPage()
        }
        tabBarController?.view.addSubview(viewTimeAdder)
    }
    
    /// To setup timer view in a collection view.
    func collectionviewSetup() {
        collectionTimer.delegate = self
        collectionTimer.dataSource = self
        collectionTimer.register(UINib(nibName: "TimerCell", bundle: nil),
                                 forCellWithReuseIdentifier: "TimerCell")
        pageCtrlCollection.hidesForSinglePage = true
        
        // Initialise UICollectionViewFlowLayout
        let layout: UICollectionViewFlowLayout = UICollectionViewFlowLayout()
        let width = self.view.frame.width
        let height = collectionTimer.frame.height
        layout.itemSize = CGSize(width: width, height: height)
        layout.sectionInset = UIEdgeInsets(top: 0, left: 0, bottom: 0, right: 0)
        layout.minimumInteritemSpacing = 0
        layout.minimumLineSpacing = 0
        layout.scrollDirection = .horizontal
        collectionTimer?.collectionViewLayout = layout
    }
    
    func collectionView(_ collectionView: UICollectionView, numberOfItemsInSection section: Int)
        -> Int {
        return 1
    }
    
    func numberOfSections(in collectionView: UICollectionView) -> Int {
        pageCtrlCollection.numberOfPages = arrRunningTask.count
        if arrRunningTask.count == 0 {
            // To show punch in timer.
            lblPunchInTimer.isHidden = true
            return 1
        }
        lblPunchInTimer.isHidden = false
        return arrRunningTask.count
    }
    
    func collectionView(_ collectionView: UICollectionView, cellForItemAt indexPath: IndexPath)
        -> UICollectionViewCell {
            let cell = collectionView.dequeueReusableCell(withReuseIdentifier: "TimerCell", for:
                indexPath) as! TimerCell
            
            // If no task s running.
            if arrRunningTask.count == 0 {
                cell.timer?.invalidate()
                cell.customInitPuncher()
                return cell
            }
            
            tasksCDController.updateUserTaskTime()
            cell.timer?.invalidate()
            cell.customInit(taskId: arrRunningTask[indexPath.section])
            return cell
    }
    
    func scrollViewDidEndDecelerating(_ scrollView: UIScrollView) {
        // Get current page
        let x = scrollView.contentOffset.x
        let w = scrollView.bounds.size.width
        let currentPage = Int(ceil(x/w))
        
        // Update visble page.
        nSelectedRunTask = currentPage
        pageCtrlCollection.currentPage = nSelectedRunTask
    }
}
