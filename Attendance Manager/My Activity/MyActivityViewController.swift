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

class MyActivityViewController: UIViewController, TableviewTap, BarChartViewDelegate {
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
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.view.addGradient(cgPStart: CGPoint(x: 0, y: 0), cgPEnd: CGPoint(x: 1, y: 1))
        
        nsLBtnWidth.constant = self.view.bounds.width / 3
        
        let cgPoint = view.center
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
    
    func alertCellSwipe() {
        //If user tries to logout ihn the break time shows alert.
        let alert = UIAlertController(title: "Alert..!", message:
            "Please stop this task, before moving to edit"
            , preferredStyle: UIAlertController.Style.alert)
        alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default,
                                      handler: { _ in
        }))
        self.present(alert, animated: true, completion: nil)
    }
    
    func cellSelected(taskId: Int) {
        ntaskId = taskId
        performSegue(withIdentifier: "SegueToTaskController", sender: self)
    }
    
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        if segue.identifier == "SegueToTaskController" {
            let taskController = segue.destination as! TaskViewController
            taskController.taskId = ntaskId
        }
    }
    
    deinit {
        print("MyActInfo Deinitialised")
    }
}
