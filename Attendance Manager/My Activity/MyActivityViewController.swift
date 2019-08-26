//
//  MyActivityViewController.swift
//  Attendance Manager
//
//  Created by Sachin on 10/1/19.
//  Copyright © 2019 Sachin. All rights reserved.
//

import UIKit

class MyActivityViewController: UIViewController, TableviewTap {
    
    @IBOutlet weak var nsLBtnWidth: NSLayoutConstraint!
    @IBOutlet weak var btnBack: UIButton!
    @IBOutlet weak var btnDaily: UIButton!
    @IBOutlet weak var btnWeekly: UIButton!
    @IBOutlet weak var btnMonthly: UIButton!
    @IBOutlet weak var lblMyAct: UILabel!
    @IBOutlet weak var viewHeader: UIView!
    
    var viewSelection: UIView!
    var arrActView: Array<ActivityView>!
    var arrDate: Array<String>?
    var headerText: String?
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        nsLBtnWidth.constant = self.view.bounds.width / 3
        
        
        setUpViews()
    }
    
    func setUpViews() {
        arrActView = Array<ActivityView>()
        for n in 0..<3 {
            let viewAct = UINib(nibName: "ActivityView", bundle: Bundle(for: ActivityView.self))
                .instantiate(withOwner: self, options: nil)[0] as! ActivityView
            
            let cgRect = CGRect(x: 0, y: viewHeader.frame.maxY + 2, width: view.bounds.width, height:
                view.bounds.height - viewHeader.frame.maxY)
            viewAct.frame = cgRect
            viewAct.customInit(sliderView: n)
            viewAct.delegate = self
//            viewAct.layer.cornerRadius = 10
//            viewAct.layer.masksToBounds = true
            arrActView.append(viewAct)
            view.addSubview(arrActView[n])
        }
        view.bringSubviewToFront(arrActView[0])
        viewHeader.addGradient(startColor: cgCForGradientStart, endColor: cgCForGradientEnd)
//        btnWeekly.backgroundColor = UIColor(cgColor: cgCForGradientStart)
//        btnMonthly.backgroundColor = UIColor(cgColor: cgCForGradientStart)
        
        let cgRect = CGRect(x: 0, y: btnDaily.frame.maxY, width: nsLBtnWidth.constant, height: 2)
        viewSelection = UIView(frame: cgRect)
        viewSelection.backgroundColor = .white
        viewSelection.layer.masksToBounds = true
        viewSelection.layer.cornerRadius = 1
        view.addSubview(viewSelection)
    }
    
    @IBAction func btnDailyPressed(_ sender: Any) {
        view.bringSubviewToFront(arrActView[0])
//        btnDaily.backgroundColor = .clear
//        btnWeekly.backgroundColor = UIColor(cgColor: cgCForGradientStart)
//        btnMonthly.backgroundColor = UIColor(cgColor: cgCForGradientStart)
        let cgPoint = CGPoint(x: btnDaily.frame.minX, y: btnDaily.frame.maxY)
        view.bringSubviewToFront(viewSelection)
        UIView.animate(withDuration: 0.2) {
            self.viewSelection.frame.origin = cgPoint
        }
    }
    
    @IBAction func btnWeeklyPressed(_ sender: Any) {
        view.bringSubviewToFront(arrActView[1])
//        btnDaily.backgroundColor = UIColor(cgColor: cgCForGradientStart)
//        btnWeekly.backgroundColor = .clear
//        btnMonthly.backgroundColor = UIColor(cgColor: cgCForGradientStart)
        let cgPoint = CGPoint(x: btnWeekly.frame.minX, y: btnWeekly.frame.maxY)
        view.bringSubviewToFront(viewSelection)
        UIView.animate(withDuration: 0.2) {
            self.viewSelection.frame.origin = cgPoint
        }
        
    }
    
    @IBAction func btnMonthlyPressed(_ sender: Any) {
        view.bringSubviewToFront(arrActView[2])
//        btnDaily.backgroundColor = UIColor(cgColor: cgCForGradientStart)
//        btnWeekly.backgroundColor = UIColor(cgColor: cgCForGradientStart)
//        btnMonthly.backgroundColor = .clear
        let cgPoint = CGPoint(x: btnMonthly.frame.minX, y: btnMonthly.frame.maxY)
        view.bringSubviewToFront(viewSelection)
        UIView.animate(withDuration: 0.2) {
            self.viewSelection.frame.origin = cgPoint
        }
    }
    
    func cellSelected(arrDates: Array<String>, strHeader: String) {
        self.arrDate = arrDates
        self.headerText = strHeader
        performSegue(withIdentifier: "SegueToChartView", sender: nil)
    }
    
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        if segue.destination is ActivityChartViewController {
            let vc = segue.destination as? ActivityChartViewController
            vc?.arrStrDate = arrDate
            vc?.strHeader = headerText
        }
    }
    
    @IBAction func btnBackPressed(_ sender: Any) {
        self.presentingViewController?.dismiss(animated: true, completion: {
            for actView in self.arrActView {
                actView.delegate = nil
            }
            self.view = nil
        })
    }
    
    deinit {
        print("MyActInfo Deinitialised")
    }
    
}
