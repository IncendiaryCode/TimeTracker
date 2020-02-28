/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : MyActivityViewController.swift
 //
 //    File Created      : 10:Oct:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : My Activities view Controller.
 //
 //////////////////////////////////////////////////////////////////////////// */

import UIKit

class MyActivityViewController: UIViewController, ActivityViewDelagate, BarChartViewDelegate
, FilterDelegate {
    
    @IBOutlet weak var nsLBtnWidth: NSLayoutConstraint!
    @IBOutlet weak var btnDaily: UIButton!
    @IBOutlet weak var btnWeekly: UIButton!
    @IBOutlet weak var btnMonthly: UIButton!
    @IBOutlet weak var lblMyAct: UILabel!
    @IBOutlet weak var tabBarTasks: UITabBarItem!
    
    var viewSelection: UIView!
    var arrActView: Array<ActivityView>!
    var ntaskId: Int!
    var headerText: String?
    var topSafeArea: CGFloat!
    var actIndicator: UIActivityIndicatorView!
    /// Page in which filter pressed.
    var nFilteringPage: Int!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.view.addGradient(cgPStart: CGPoint(x: 0, y: 0), cgPEnd: CGPoint(x: 1, y: 1))
        
        nsLBtnWidth.constant = self.view.bounds.width / 3
        
        let screenBounds = UIScreen.main.bounds
        let cgPoint = CGPoint(x: screenBounds.midX, y: screenBounds.midY)
        let cgSize = CGSize(width: 40, height: 40)
        
        // Setup activity indicator to setup view.
        actIndicator = UIActivityIndicatorView()
        actIndicator.color = .white
        actIndicator.tintColor = .white
        actIndicator.center = cgPoint
        actIndicator.frame.size = cgSize
        view.addSubview(actIndicator)
        actIndicator.startAnimating()
    }
    
    func updateViewsAndColor() {
        self.view.addGradient(cgPStart: CGPoint(x: 0, y: 0), cgPEnd: CGPoint(x: 1, y: 0.3))
        self.view.layoutIfNeeded()
    }
    
    override func viewDidAppear(_ animated: Bool) {
        super.viewDidAppear(true)
        if nil == arrActView {
            // Setup initial viewDidAppear.
            self.setUpViews()
            arrActView[0].viewGraphXAxis.isHidden = false
            arrActView[0].setupDayView()
            actIndicator.stopAnimating()
            
            // Remove first layer and add again gradient only to top.
            self.view.layer.sublayers?.removeFirst()
            self.view.addGradient(cgPStart: CGPoint(x: 0, y: 0), cgPEnd: CGPoint(x: 1, y: 0.3))
        }
        // Reload selected view to make animation.
        switch viewSelection.frame.midX {
            case btnDaily.frame.midX: arrActView[0].viewGraphXAxis.isHidden = false
                                      arrActView[0].setupDayView()
            
            case btnWeekly.frame.midX: arrActView[1].resetWeekBar()
                                       arrActView[1].setupWeekView()
            
            case btnMonthly.frame.midX: arrActView[2].setupMonthView()
            
            default:
                break
        }
        
    }
    
    func tabBarController(_ tabBarController: UITabBarController, didSelect viewController:
            UIViewController) {
        let tabBarIndex = tabBarController.selectedIndex
        if tabBarIndex == 0 {
            
        }
    }
    
    func updateCoredataTimings() {
        // update user work time task timings.
        let tasksCDController = TasksCDController()
        tasksCDController.updateUserTaskTime()
    }
    
    func setUpViews() {
        arrActView = Array<ActivityView>()
        for n in 0..<3 {
            // Create 3 Activity view object (Day, Week and Month view).
            let viewAct = UINib(nibName: "ActivityView", bundle: Bundle(for: ActivityView.self))
                .instantiate(withOwner: self, options: nil)[0] as! ActivityView

            // Set up constraints.
            let cgRect = CGRect(x: 0, y: btnDaily.frame.maxY+10, width: view.bounds.width,
                                height: view.bounds.height - btnDaily.frame.maxY)
            viewAct.frame = cgRect
            viewAct.customInit(sliderView: n)
            viewAct.delegate = self
            viewAct.delegateChart = self
            viewAct.calendarView.delegateChart = self
            arrActView.append(viewAct)
            view.addSubview(arrActView[n])
        }
        // ViewSelection highlights selected one among 3 different views.
        view.bringSubviewToFront(arrActView[0])
        let x = nsLBtnWidth.constant / 4
        let width = nsLBtnWidth.constant - 2*x
        let cgRect = CGRect(x: x, y: btnDaily.frame.maxY-4, width: width, height: 2)
        viewSelection = UIView(frame: cgRect)
        viewSelection.backgroundColor = .white
        viewSelection.layer.masksToBounds = true
        viewSelection.layer.cornerRadius = 2
        view.addSubview(viewSelection)
    }
    
    @IBAction func btnDailyPressed(_ sender: Any) {
        // Bring day view to front.
        updateCoredataTimings()
        view.bringSubviewToFront(arrActView[0])
        let cgPoint = CGPoint(x: btnDaily.frame.maxX/4, y: btnDaily.frame.maxY-4)
        view.bringSubviewToFront(viewSelection)
        // Setup selection area highlighter.
        UIView.animate(withDuration: 0.2) {
            self.viewSelection.frame.origin = cgPoint
        }
        arrActView[0].setupDayView()
    }
    
    @IBAction func btnWeeklyPressed(_ sender: Any) {
        updateCoredataTimings()
        view.bringSubviewToFront(arrActView[1])
        let cgPoint = CGPoint(x: btnWeekly.frame.minX + btnDaily.frame.maxX/4,
                              y: btnWeekly.frame.maxY-4)
        view.bringSubviewToFront(viewSelection)
        arrActView[1].resetWeekBar()
        arrActView[1].setupWeekView()
        // Setup selection area highlighter.
        UIView.animate(withDuration: 0.2) {
            self.viewSelection.frame.origin = cgPoint
        }
    }
    
    @IBAction func btnMonthlyPressed(_ sender: Any) {
        updateCoredataTimings()
//        arrActView[2].updateMonthDataSource()
        arrActView[2].nSelectedIndexMonth = 0 // display current month.
        arrActView[2].calendarView.setDisplayDate(Date())
        arrActView[2].dateCurrentMonth = arrActView[2].calendarView.displayDate
        let strMonth = arrActView[2].calendarView.dateOnHeader(arrActView[2].dateCurrentMonth)
        arrActView[2].lblDate.text = strMonth

        view.bringSubviewToFront(arrActView[2])
        let cgPoint = CGPoint(x: btnMonthly.frame.minX+btnDaily.frame.maxX/4,
                              y: btnMonthly.frame.maxY-4)
        view.bringSubviewToFront(viewSelection)
        // Setup selection area highlighter.
        UIView.animate(withDuration: 0.2) {
            self.viewSelection.frame.origin = cgPoint
        }
        // Update task timings.
        arrActView[2].setupMonthView()
    }
    
    func chartPressed(intDate: Int64) {
        updateCoredataTimings()
        if let index = arrActView[0].arrIntDate.firstIndex(of: intDate) {
            // Bring day view front based on selected date.
            view.bringSubviewToFront(arrActView[0])
            arrActView[0].indexSelDate = index
            arrActView[0].setupDayView()
            let cgPoint = CGPoint(x: btnDaily.frame.maxX/4, y: btnDaily.frame.maxY-4)
            view.bringSubviewToFront(viewSelection)
            UIView.animate(withDuration: 0.2) {
                self.viewSelection.frame.origin = cgPoint
            }
        }
    }
    
    func noData() {
        //Alert while puch in.
        let alert = UIAlertController(title: "Message", message: "There is no data on this date",
                                      preferredStyle: UIAlertController.Style.alert)
        alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default,
                                      handler: {(_: UIAlertAction!) in
        }
        ))
        self.present(alert, animated: true, completion: nil)
    }
    
    func cellSwipeToStop(taskId: Int, pageNo: Int) {
        //If user tries to logout ihn the break time shows alert.
        if let userActivtyVC = tabBarController?.viewControllers?[1] as? DashboardVC {
            userActivtyVC.stopTask(taskId: taskId, pageNo: pageNo) {
                // Refresh history views.
                self.arrActView[0].setupDayView()
                self.arrActView[1].resetWeekBar()
                self.arrActView[1].setupWeekView()
                self.arrActView[2].setupMonthView()
            }
        }
    }
    
    func cellSelected(taskId: Int) {
        ntaskId = taskId
        performSegue(withIdentifier: "SegueToTaskController", sender: self)
    }
    
    func showIntroPageDayView() {
        let cgRect = CGRect(x: arrActView[0].btnLeftMove.frame.minX
            , y: arrActView[0].btnLeftMove.frame.minY+viewSelection.frame.minY+15, width: 44
            , height: 44)
        let userGuideData = UserguideData(itemFrame: cgRect, itemHint:
            "To check previous working day's task details.")
        let viewUserGuideTask = UserguideView(userguideData: userGuideData)
        viewUserGuideTask.completionHandler = {
        }
        UserDefaults.standard.set(true, forKey: "IntroStatusDayLeft")
        tabBarController?.view.addSubview(viewUserGuideTask)
    }
    
    func showIntroPageWeekView() {
        let cgRect = CGRect(x: arrActView[0].btnLeftMove.frame.minX
            , y: arrActView[0].btnLeftMove.frame.maxY+viewSelection.frame.minY, width: 44
            , height: 44)
        let userGuideData = UserguideData(itemFrame: cgRect, itemHint:
            "To check previous working week's task details.")
        let viewUserGuideTask = UserguideView(userguideData: userGuideData)
        viewUserGuideTask.completionHandler = {
        }
        UserDefaults.standard.set(true, forKey: "IntroStatusWeekLeft")
        tabBarController?.view.addSubview(viewUserGuideTask)
    }
    
    func btnFilterPressed(page: Int) {
        nFilteringPage = page
        performSegue(withIdentifier: "ShowFilter", sender: nil)
        self.view.alpha = 0.2
    }
    
    func selectedProjects(arrProj: Array<Int>) {
        // Display only selected projects.
        arrActView[nFilteringPage].arrSelectedProj = Array<Int>()
        arrActView[nFilteringPage].arrSelectedProj = arrProj
        refreshActivity()
        self.view.alpha = 1
    }
    
    func refreshActivity() {
        if nFilteringPage == 0 {
            arrActView[0].setupDayView()
        }
        else if nFilteringPage == 1 {
            arrActView[1].resetWeekBar()
            arrActView[1].setupWeekView()
        }
        else {
            arrActView[2].setupMonthView()
        }
    }
    
    func dismissedView() {
        // Dismissed view from filter view.
        self.view.alpha = 1
    }
    
    func clearedFilter() {
        // Apply clear filter.
        arrActView[nFilteringPage].arrSelectedProj = nil
        self.view.alpha = 1
        refreshActivity()
    }
    
    func sortApplied(sortType: SortTypes, indexSelected: IndexPath) {
        self.view.alpha = 1
    }
    
    func changedFilterViewPosition(cgFAlpha: CGFloat) {
        if cgFAlpha >= 0.2 {
            self.view.alpha = cgFAlpha
        }
    }
    
    func refreshData(completion: @escaping (() -> Void) = {}) {
        if let dashboardVC = tabBarController?.viewControllers![1] as? DashboardVC {
            dashboardVC.fetchTaskDataFromServer(completion: completion)
        }
    }
    
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        if segue.destination is FilterViewController {
            let vc = segue.destination as? FilterViewController
            vc?.delegate = self
            if let arrProj = arrActView[nFilteringPage].arrSelectedProj {
                // If already filter applied pass the filter values to filter view.
                vc?.arrSelectedProj = Array<Int>()
                vc?.arrSelectedProj = arrProj
            }
        }
        else if segue.identifier == "SegueToTaskController" {
            let taskController = segue.destination as! TaskViewController
            taskController.taskId = ntaskId
        }
    }
    
    deinit {
        print("MyActInfo Deinitialised")
    }
}
