
//
//  ActivityView.swift
//  Attendance Manager
//
//  Created by Sachin on 10/1/19.
//  Copyright © 2019 Sachin. All rights reserved.
//
protocol TableviewTap {
    func cellSelected(arrDates: Array<String>, strHeader: String)
}


import UIKit
import Charts

class ActivityView: UIView, UITableViewDelegate, UITableViewDataSource {
    var nCell: Int!
    var nSliderView: Int!
    var userActUpdater: UserActivityUpdater!
    var projectUpdater: AddProjects!
    var taskUpdater: TaskUpdater!
    var arrDictTaskDetails: Array<Dictionary<String, Any>>!
    var delegate: TableviewTap?
    var headerText: String!
    
    @IBOutlet weak var tblActivities: UITableView!
    
    override init(frame: CGRect) {
        super.init(frame: frame)
    }
    
    func customInit(sliderView: Int) {
        nSliderView = sliderView
        userActUpdater = UserActivityUpdater()
        projectUpdater = AddProjects()
        taskUpdater = TaskUpdater()
        headerText = "sfs"
        setUpView()
    }
    
    func setUpView() {
        if nSliderView == 0 {
            arrDictTaskDetails = taskUpdater.getDayWiseDetails()
        }
        else if nSliderView == 1 {
            arrDictTaskDetails = taskUpdater.getWeekWiseDetails()
        }
        else {
            arrDictTaskDetails = taskUpdater.getMonthWiseDetails()
        }
        nCell = arrDictTaskDetails.count
    }
    
    override func awakeFromNib() {
        super.awakeFromNib()
        tblActivities.delegate = self
        tblActivities.dataSource = self
        tblActivities.register(UINib(nibName: "ActivityCellController", bundle: nil),
        forCellReuseIdentifier: "ActivityCell")
    }
    
    required init?(coder aDecoder: NSCoder) {
       super.init(coder: aDecoder)
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return nCell
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "ActivityCell", for: indexPath)
            as! ActivityCellController
        if nSliderView == 0 {
            let dictValue = arrDictTaskDetails[indexPath.row]
            let strDate = dictValue["Date"] as! String
            let words = strDate.split(separator: "/")
            let nMonth = Int(words[1])
            let monthStr = Calendar.current.monthSymbols[nMonth! - 1]
            let start = String.Index(utf16Offset: 0, in: monthStr)
            let end = String.Index(utf16Offset: 3, in: monthStr)
            let strMon = String(monthStr[start..<end])
            print("Date\(strMon)")
            cell.lblSubDayWeek.text = strMon
            cell.lblDayWeekMonth.text = String(words[0])
            let strTotWork = getSecondsToHoursMinutesSeconds(seconds: dictValue["Total Work"]
                as! Int)
            print("strToWork\(strTotWork)")
            cell.lblNWorkTime.text = "\(strTotWork)"
            let nTask = dictValue["Task Count"]
            print("nTask\(nTask!)")
            cell.lblNTaskcount.text = "\(nTask!)"
            cell.lblWorkDays.text = ""
            cell.lblNWorks.text = ""
            
        }
        else if nSliderView == 1 {
            let dictValue = arrDictTaskDetails[indexPath.row]
            let strTotWork = getSecondsToHoursMinutesSeconds(seconds: dictValue["Total Work"]
                    as! Int)
            cell.lblNWorkTime.text = "\(strTotWork)"
            let nTask = dictValue["Task Count"]
            cell.lblNTaskcount.text = "\(nTask!)"
            let workDays = dictValue["Days"] as! Int
            cell.lblNWorks.text = "\(workDays)"
            cell.lblSubDayWeek.text = "week"
            let week = dictValue["Week"] as! Int
            cell.lblDayWeekMonth.text = "\(week)"
        }
        else {
            let dictValue = arrDictTaskDetails[indexPath.row]
            let strTotWork = getSecondsToHoursMinutesSeconds(seconds: dictValue["Total Work"]
                    as! Int)
            cell.lblNWorkTime.text = "\(strTotWork)"
            let nTask = dictValue["Task Count"]
            cell.lblNTaskcount.text = "\(nTask!)"
            let workDays = dictValue["Days"] as! Int
            cell.lblNWorks.text = "\(workDays)"
            let year = dictValue["Year"] as! String
            cell.lblSubDayWeek.text = year
            let month = dictValue["Month"] as! String
            cell.lblDayWeekMonth.text = "\(month)"
        }
        cell.selectionStyle = .none
        return cell
    }
    
    func drawHorizontalBarView(cell: ActivityCellController) {
        if cell.horizontalBarView.data == nil {
        
            var values: Array<Int> = []
            var labels: Array<String> = []
            
            for dictValues in arrDictTaskDetails {
                let strName = "abc"
                let totTime = dictValues["Total Work"] as! Int
                labels.append(strName)
                values.append(totTime)
            }
            
            
            
//        let values = [1000, 2000, 3000, 5000, 7000, 8000, 15000, 21000, 22000, 35000]
//        let labels = ["Blue Yonder Airlines", "Aaron Fitz Electrical", "Central Communications", "Magnificent Office Images", "Breakthrough Telemarke", "Lawrence Telemarketing", "Vancouver Resort Hotels", "Mahler State University", "Astor Suites", "Plaza One"]



        var dataEntries = [ChartDataEntry]()

        for i in 0..<values.count {
            let entry = BarChartDataEntry(x: Double(i), y: Double(values[i]))

            dataEntries.append(entry)
        }

        let barChartDataSet = BarChartDataSet(entries: dataEntries, label: "")
        barChartDataSet.drawValuesEnabled = false
        barChartDataSet.colors = ChartColorTemplates.joyful()

        let barChartData = BarChartData(dataSet: barChartDataSet)
        cell.horizontalBarView.data = barChartData
        cell.horizontalBarView.legend.enabled = false

        cell.horizontalBarView.xAxis.valueFormatter = IndexAxisValueFormatter(values: labels)
        cell.horizontalBarView.xAxis.granularityEnabled = true
        cell.horizontalBarView.xAxis.granularity = 1

        cell.horizontalBarView.animate(xAxisDuration: 3.0, yAxisDuration: 3.0, easingOption: .easeInOutBounce)

//        cell.horizontalBarView.chartDescription?.text = ""
//
//
//        cell.horizontalBarView.zoom(scaleX: 1.1, scaleY: 1.0, x: 0, y: 0)
//
        let rightAxis = cell.horizontalBarView.rightAxis
        rightAxis.drawGridLinesEnabled = false
//
        let leftAxis = cell.horizontalBarView.leftAxis
        leftAxis.drawGridLinesEnabled = false

        let xAxis = cell.horizontalBarView.xAxis
        xAxis.drawGridLinesEnabled = false
//        cell.horizontalBarView.setVisibleXRange(minXRange: 8.0, maxXRange: 8.0)
//
//        cell.horizontalBarView.setExtraOffsets (left: 0, top: 20.0, right:0.0, bottom: 20.0)
            
        }
    }
    
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
        return 120
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        let dictValue = arrDictTaskDetails[indexPath.row]
        let cell = tableView.cellForRow(at: indexPath) as!  ActivityCellController
        if nSliderView == 0 {
            let arrDate = [dictValue["Date"] as! String]
            let date = cell.lblDayWeekMonth.text!
            let strMon = cell.lblSubDayWeek.text!
            headerText = "\(date) \(strMon)"
            self.delegate?.cellSelected(arrDates: arrDate, strHeader: headerText)
        }
        else if nSliderView == 1 {
            let date = cell.lblDayWeekMonth.text!
            let strMon = cell.lblSubDayWeek.text!
            headerText = "\(strMon) \(date) "
            self.delegate?.cellSelected(arrDates: dictValue["Date"] as! Array<String>, strHeader: headerText)
        }
        else {
            let date = cell.lblDayWeekMonth.text!
            let strMon = cell.lblSubDayWeek.text!
            headerText = "\(date) \(strMon)"
            self.delegate?.cellSelected(arrDates: dictValue["Date"] as! Array<String>,
                                        strHeader: headerText)
        }
    }
    
}
extension Date {
    var month: String {
        let dateFormatter = DateFormatter()
        dateFormatter.dateFormat = "MMM"
        return dateFormatter.string(from: self)
    }
}
