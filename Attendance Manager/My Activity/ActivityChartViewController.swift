//
//  ActivityChartViewController.swift
//  Attendance Manager
//
//  Created by Sachin on 10/4/19.
//  Copyright © 2019 Sachin. All rights reserved.
//

import UIKit
import Charts

class ActivityChartViewController: UIViewController, UITableViewDelegate, UITableViewDataSource, ChartViewDelegate {
    
    @IBOutlet weak var btnBack: UIButton!
    @IBOutlet weak var viewHeader: UIView!
    @IBOutlet weak var lblHeader: UILabel!
    var pieChartView: PieChartView!
    @IBOutlet weak var tbleTaskView: UITableView!
    
    var indexSelected: IndexPath?
    var arrDictTaskDetails: Array<Dictionary<String, Any>>!
    var taskUpdater: TaskUpdater!
    var projectUpdater: AddProjects!
    var arrStrDate: Array<String>!
    var uiColors: [UIColor]!
    var strHeader: String!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        viewHeader.addGradient(startColor: cgCForGradientStart, endColor: cgCForGradientEnd)
        lblHeader.text = strHeader
        taskUpdater = TaskUpdater()
        projectUpdater = AddProjects()
        arrDictTaskDetails = taskUpdater.getDataFromDate(arrDate: arrStrDate)
        let width = UIScreen.main.bounds.width
        let cgRect = CGRect(x: 0, y: 0, width: width, height: width)
        pieChartView = PieChartView(frame: cgRect)
        pieChartView.delegate = self
        pieChartView.usePercentValuesEnabled = true
        
        pieChartView.layer.cornerRadius = 35
        pieChartView.layer.masksToBounds = true
        pieChartView.backgroundColor = .white
        pieChartView.drawEntryLabelsEnabled = false
        pieChartView.holeRadiusPercent = 0.4
        
        var totTime = 0
        for dictValues in arrDictTaskDetails {
            let time = dictValues["Total Time"] as! Int
            totTime += time
        }
        
        let attrString = NSMutableAttributedString(string:
            "Total Work\n\(getSecondsToHoursMinutesSeconds(seconds: totTime))")
        let paragraphStyle = NSParagraphStyle.default.mutableCopy() as! NSMutableParagraphStyle
                       paragraphStyle.lineBreakMode = .byTruncatingTail
                       paragraphStyle.alignment = .center
        attrString.setAttributes([
                .foregroundColor: NSUIColor.lightGray,
                .font: NSUIFont.systemFont(ofSize: 12.0),
                .paragraphStyle: paragraphStyle
                ], range: NSMakeRange(0, attrString.length))
        
        pieChartView.centerAttributedText = attrString
        
        
        
        pieChartView.legend.enabled = false
        var strTaskName = Array<String>()
        var goals = Array<Int>()
        uiColors = [UIColor]()
        
        for dictValues in arrDictTaskDetails {
            let strName = dictValues["Task Name"] as! String
            let totTime = dictValues["Total Time"] as! Int
            strTaskName.append(strName)
            goals.append(totTime)
        }
    
        customizeChart(dataPoints: strTaskName, values: goals.map{ Double($0) })
        tbleTaskView.delegate = self
        tbleTaskView.dataSource = self
        tbleTaskView.register(UINib(nibName: "userBreakInfoCell", bundle: nil),
        forCellReuseIdentifier: "userBreakInfoCell")
    }
    
    func chartValueSelected(_ chartView: ChartViewBase, entry: ChartDataEntry,
                            highlight: Highlight) {
         if let dataSet = chartView.data?.dataSets[ highlight.dataSetIndex] {
            let sliceIndex: Int = dataSet.entryIndex( entry: entry)
            tbleTaskView.scrollToRow(at: IndexPath(row: sliceIndex + 1, section: 0), at: .top,
                                     animated: true)
            indexSelected = IndexPath(row: sliceIndex + 1, section: 0)
            tbleTaskView.reloadData()
        }
    }
    
    func chartTranslated(_ chartView: ChartViewBase, dX: CGFloat, dY: CGFloat) {
        print("lshfhsfhiosfhyisfisy")
    }
    
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
        if indexPath == IndexPath(row: 0, section: 0) {
            return UIScreen.main.bounds.width
        }
        else {
            let row = indexPath.row - 1
            let index = IndexPath(row: row, section: 0)
            let dictValue = arrDictTaskDetails[index.row]
            let taskName = dictValue["Task Name"] as! String
            
            let label = UILabel(frame: CGRect(x: 0, y: 0, width: UIScreen.main.bounds.width - 57, height: 20))
            label.numberOfLines = 0
            label.lineBreakMode = .byWordWrapping
            label.text = taskName
            label.sizeToFit()
            
            print("soydfutus \(label.frame)")
            
            return 94 + label.bounds.height
        }
    }
    
    func customizeChart(dataPoints: [String], values: [Double]) {
        var dataEntries: [ChartDataEntry] = []
        var iMult: CGFloat = 20.0
        for i in 0..<dataPoints.count {
            let dataEntry = PieChartDataEntry(value: values[i], label: dataPoints[i])
            if iMult*5 > 255 {
                iMult = 5
            }
            
            let uiColor = UIColor(red: .random(in: 40...255)/255,
            green: .random(in: 100...180)/255,
            blue: .random(in: 230...255)/255,
            alpha: 1.0)
            
//            let uiColor = UIColor(red: iMult*5/255, green: CGFloat(105+iMult)/255, blue: CGFloat(105+iMult)/255, alpha: 1.0)
            iMult += 5
            self.uiColors.append(uiColor)
            dataEntries.append(dataEntry)
        }
        // 2. Set ChartDataSet
        let pieChartDataSet = PieChartDataSet(entries: dataEntries, label: nil)
        
        pieChartDataSet.colors = uiColors
        // 3. Set ChartData
        let pieChartData = PieChartData(dataSet: pieChartDataSet)
        let formatter = NumberFormatter()
        formatter.numberStyle = .percent
        formatter.maximumFractionDigits = 1
        formatter.multiplier = 1.0
        let format = DefaultValueFormatter(formatter: formatter)
        pieChartData.setValueFormatter(format)
            
        // 4. Assign it to the chart’s data
        pieChartView.data = pieChartData
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrDictTaskDetails.count + 1
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        if indexPath == IndexPath(row: 0, section: 0) {
            let width = UIScreen.main.bounds.width
            let cgRect = CGRect(x: 0, y: 0, width: width, height: width)
            let cell = UITableViewCell(frame: cgRect)
            cell.addSubview(pieChartView)
            cell.selectionStyle = .none
            return cell
        }
        else {
            let row = indexPath.row - 1
            let index = IndexPath(row: row, section: 0)
            let cell = tableView.dequeueReusableCell(withIdentifier: "userBreakInfoCell",
                                   for: indexPath) as! UserTaskInfoCell
            let dictValues = arrDictTaskDetails[index.row]
                           
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
            
            cell.lblCategory.backgroundColor = uiColors[index.row]
            cell.lblTaskDescription.text = "\(dictValues["Task Name"]!)"
            let projId = dictValues["Project Id"]! as! Int
            cell.lblProjectName.text = "\(projectUpdater.getProjectName(projId: projId))"
            cell.nTaskId = indexPath.row + 1
            downloadImage(from: projectUpdater.getProjectIconUrl(projectId: projId),
                                         imgView: cell.imgVProjectIcon)
            cell.selectionStyle = .none
            cell.contentView.backgroundColor = .clear
            if indexSelected != nil && indexSelected == indexPath {
                cell.contentView.backgroundColor = UIColor(red: 140/255, green: 20/255,
                                                           blue: 252/255, alpha: 0.2)
            }
            return cell
        }
    }
 
    @IBAction func btnBackPressed(_ sender: Any) {
        self.presentingViewController?.dismiss(animated: true, completion: {self.view = nil})
    }
    
    deinit {
        print("Chart View Deinitialised")
    }
}
