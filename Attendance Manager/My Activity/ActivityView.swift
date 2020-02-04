/*//////////////////////////////////////////////////////////////////////////////
//
//    Copyright (c) GreenPrint Technologies LLC. 2019
//
//    File Name         : ActivityView.swift
//
//    File Created      : 10:Oct:2019
//
//    Dev Name          : Sachin Kumar K.
//
//    Description       : My Activities view.
//
//////////////////////////////////////////////////////////////////////////// */

protocol TableviewTap {
	/// Delegate sends selected cell's task id.
	func cellSelected(taskId: Int)
	/// When swipe action performed to cell and that cell belongs to running task.
	func cellSwipeToStop(taskId: Int)
	/// Show intro page in day view.
	func showIntroPageDayView()
	/// Show intro page in week view.
	func showIntroPageWeekView()
}

protocol BarChartViewDelegate {
	/// Delegate sends timesince1970 object.
	func chartPressed(intDate: Int64)
	func noData()
}

import UIKit

class ActivityView: UIView, UITableViewDelegate, UITableViewDataSource, CalendarViewDataSource,
	CalendarViewDelegate {
	
	/// Cell count different for 3 views.
    var nCell: Int!
	/// View recognizer (day or week or month)
    var nSliderView: Int!
	var punchInOutCDCtrlr: PunchInOutCDController!
    var tasksCDCtrlr: TasksCDController!
    var tasksTimeCDCtrlr: TasksTimeCDController!
	
	/// Stores all tasks times. (Required in daily view, multiple timings)
    var arrCTaskTimeDetails: Array<TaskTimeDetails>!
	/// Stores all task details.
	var arrCTaskDetails: Array<TaskDetails>!
	/// Delegate to intaract daily and week view.
	var delegateChart: BarChartViewDelegate?
	/// Delegate when table cell tapped.
    var delegate: TableviewTap?
	
	/// To store week details.(week number, total work in week, etc)
    var arrWeekDetails: Array<WeekDetails>!
	/// To store month details. (month, total work in month, etx)
	var arrDictMonthData: Array<MonthDetails>!
	/// Date object of currently displaying month.
	var dateCurrentMonth: Date!
	/// Calendar month indicator. (0 for current month, -1 for previous month)
	var nSelectedIndexMonth: Int!
	/// Week button minY value.
	var cgFButtonMinY: CGFloat!
	/// Array of buttons to display graph in daily view.
	var arrBtnsDayTask: Array<ButtonDayGraph>!
	/// X axis labels are drawn in this view.
	var viewGraphXAxis: UIView!
	/// Buttons for graph in week view.
	var arrBtnWeekView: Array<ButtonWeekGraph>!
	/// Week count in year.
	var nWeek = 0
	/// Array of dates in a week(Only work days). Required to fetch task details from this dates.(Same array date used in daily view)
	var arrIntDate: Array<Int64>!
	/// Label to display start and end timings. (When long press on daily graph)
	var lblStartEndTime: UILabel!
	/// Heighlighted button graph Width
	var cgPBtnDayGraphWidth: CGRect!
	/// selected day in day view.
	var indexSelDate = 0
	/// Label to show no data.
	var lblNoData: UILabel!
	
	@IBOutlet weak var nsLBarViewHeight: NSLayoutConstraint!
	@IBOutlet weak var btnLeftMove: UIButton!
	@IBOutlet weak var btnRightMove: UIButton!
	@IBOutlet weak var lblTotalHr: UILabel!
	@IBOutlet weak var lblDate: UILabel!
	@IBOutlet weak var viewDayAndWeekChanger: UIView!
    @IBOutlet weak var tblActivities: UITableView!
    @IBOutlet var barChartView: UIView!
	@IBOutlet weak var calendarView: CalendarView!

	override func awakeFromNib() {
        super.awakeFromNib()
		punchInOutCDCtrlr = PunchInOutCDController()
		tasksCDCtrlr = TasksCDController()
		tasksTimeCDCtrlr = TasksTimeCDController()
        tblActivities.delegate = self
        tblActivities.dataSource = self
        tblActivities.register(UINib(nibName: "userBreakInfoCell", bundle: nil),
							   forCellReuseIdentifier: "userBreakInfoCell")
		tblActivities.contentInset = UIEdgeInsets(top: -22, left: 0, bottom: 0, right: 0)
		tblActivities.layer.masksToBounds = true
		tblActivities.layer.borderWidth = 0.25
		tblActivities.layer.borderColor = g_colorMode.lineColor().cgColor
		
		nCell = 0
		backgroundColor = g_colorMode.defaultColor()
		layer.borderColor = g_colorMode.textColor().cgColor
		layer.borderWidth = 0.3
		tblActivities.backgroundColor = g_colorMode.defaultColor()
		lblDate.textColor = g_colorMode.textColor()
		lblTotalHr.textColor = g_colorMode.textColor()
    }
	
	/// Update colors (If display mode changed.. Call this function)
	func updateColorMode() {
		backgroundColor = g_colorMode.defaultColor()
		tblActivities.backgroundColor = g_colorMode.defaultColor()
	}
    
    func customInit(sliderView: Int) {
		viewDayAndWeekChanger.isHidden =  false
        nSliderView = sliderView
        arrIntDate = []
		
		// Setup label no data indicator
		let cgRect = CGRect(x: 0, y: tblActivities.frame.minY + 40
			, width: UIScreen.main.bounds.width, height: 30)
		lblNoData = UILabel(frame: cgRect)
		//			lblNoData.center = tblActivities.center
		lblNoData.text = "No task available."
		lblNoData.textAlignment = .center
		lblNoData.textColor = g_colorMode.textColor()
		lblNoData.isHidden = true
		self.addSubview(lblNoData)
		
        updateChartWithData()
		if nSliderView == 0 {
			// Set label for long press in day graph.
			let cgRect = CGRect(x: 0, y: 0, width: 150, height: 20)
			lblStartEndTime = UILabel(frame: cgRect)
			lblStartEndTime.layer.masksToBounds = true
			lblStartEndTime.backgroundColor = .gray
			lblStartEndTime.layer.cornerRadius = 10
			lblStartEndTime.font = lblStartEndTime.font.withSize(12)
			lblStartEndTime.textAlignment = .center
			lblStartEndTime.textColor = .white
			addSubview(lblStartEndTime)
			lblStartEndTime.isHidden = true
		}
    }
    
	func startDate() -> Date {
		var dateComponents = DateComponents()
		// Calendar starts from 20 years back.
		dateComponents.month = -240
		let today = Date()
		let years20 = self.calendarView.calendar.date(byAdding: dateComponents, to: today)
		return years20!
	}
	
	func endDate() -> Date {
		var dateComponents = DateComponents()
		// Calendar till current month.
		dateComponents.month = 0
		let today = Date()
		let currentMonth = self.calendarView.calendar.date(byAdding:
			dateComponents, to: today)!
		return currentMonth
	}
	
	func headerString(_ date: Date) -> String? {
		return nil
	}
	
	func calendar(_ calendar: CalendarView, didScrollToMonth date: Date) {
		if date.month != dateCurrentMonth.month {
			// Set month diffrence.
			nSelectedIndexMonth = Date().months(from: date)
			
			let strMonth = calendarView.dateOnHeader(date)
			lblDate.text = strMonth
			if arrDictMonthData.count > nSelectedIndexMonth {
				// If month exists set up details.
				let mothDetails = arrDictMonthData[nSelectedIndexMonth]
				let arrIntDate = mothDetails.arrDates
				let totWork = mothDetails.totalWork
				let strTotWork = getSecondsToHoursMinutesSeconds(seconds: totWork)
				lblTotalHr.text = "Duration: \(strTotWork)"
				arrCTaskDetails = tasksCDCtrlr.getDataFromDate(arrDate: arrIntDate)
				nCell = arrCTaskDetails.count
			}
			else {
				// If there is no working day in a month.
				nCell = 0
				lblTotalHr.text = "Duration: 00:00:00"
			}
			dateCurrentMonth = date
			tblActivities.reloadDataWithAnimation()
			checkArrowAlpha()
		}
	}
	
	func calendar(_ calendar: CalendarView, didSelectDate date: Date, withEvents events:
		[CalendarEvent]) {
		
	}
	
	func calendar(_ calendar: CalendarView, canSelectDate date: Date) -> Bool {
		return true
	}
	
	func calendar(_ calendar: CalendarView, didDeselectDate date: Date) {
		
	}
	
	func calendar(_ calendar: CalendarView, didLongPressDate date: Date, withEvents events:
		[CalendarEvent]?) {
		
	}
	
	/// Calculate y value to draw button in a day view graph.
	func findMinYforDayBtnGraph(start: Int, end: Int, minY: Int, dictDrawnPoint :
		Dictionary<Int, Array<Array<Int>>>) -> Int {
		var cgFMinY = minY
		if let arrPoints = dictDrawnPoint[minY] {
			for points in arrPoints {
				// If start time between any other timings.
				if start >= points[0] && start <= points[1] {
					cgFMinY = findMinYforDayBtnGraph(start: start, end: end, minY: minY-20,
							dictDrawnPoint: dictDrawnPoint)
				}
				// If start and end time inside any timings.
				else if start <= points[0] && end >= points[1] {
					cgFMinY = findMinYforDayBtnGraph(start: start, end: end, minY: minY-20,
							dictDrawnPoint: dictDrawnPoint)
				}
				// If start and end time between any other end time.
				else if start <= points[1] && end >= points[1] {
					cgFMinY = findMinYforDayBtnGraph(start: start, end: end, minY: minY-20,
							dictDrawnPoint: dictDrawnPoint)
				}
			}
		}
		return cgFMinY
	}
	
	// Requires when overlap scenario occur
	func drawDayDetailsGraph() {
		var dictDrawnPoints = Dictionary<Int, Array<Array<Int>>>()
		var i = 0
		
		var arrReverseSort: Array<TaskTimeDetails> = arrCTaskTimeDetails
		
		// Sort based on end time.
		arrReverseSort.sort { (task1, task2) -> Bool in
			return task1.nEndTime < task2.nEndTime
		}

		// Sort based on start time.
		arrReverseSort.sort { (task1, task2) -> Bool in
			return task1.nStartTime < task2.nStartTime
		}

		// Sort array details based on date.
		arrReverseSort.sort { (task1, task2) -> Bool in
			return getDateFromString(strDate: task1.strDate) > getDateFromString(strDate:
				task2.strDate)
		}
		
		for cTaskDetails in arrReverseSort {
			let startTime = cTaskDetails.nStartTime!
			let endTime = cTaskDetails.nEndTime!
			let taskId = cTaskDetails.taskId
			
			// Calculation based on total work time from 8AM to 8PM
			// 28800sec = 8AM
			// 43200sec = 8PM
			let startX = (CGFloat(startTime - 28800) * (UIScreen.main.bounds.width-40) / 43200)
				+ 20 // (-40) for left and right safe area, (+20) to start from left safe area.
			let endX = (CGFloat(endTime - 28800) * (UIScreen.main.bounds.width-40) / 43200)
				+ 20
			
			// Get y position for drawing.
			let minY = findMinYforDayBtnGraph(start: startTime, end: endTime, minY: 103,
								dictDrawnPoint: dictDrawnPoints)
			let cgRect = CGRect(x: startX, y: CGFloat(minY), width: 0, height: 20)
			
			// Update drawn x and y positions to dictionsary.
			if var arrValue = dictDrawnPoints[minY] {
				arrValue.append([startTime, endTime])
				dictDrawnPoints.updateValue(arrValue, forKey: minY)
			}
			else {
				dictDrawnPoints.updateValue([[startTime, endTime]], forKey: minY)
			}
			// Setup button for graph
			let btnTaskGraph = ButtonDayGraph(frame: cgRect)
			btnTaskGraph.layer.borderColor = g_colorMode.defaultColor().cgColor
			btnTaskGraph.layer.borderWidth = 1
	
			// Negative beacause its sorted in reverse.
			btnTaskGraph.tag = arrReverseSort.count - i - 1 // Tag used to identify each button.
			
			let tapGesture = UITapGestureRecognizer(target: self, action:
				#selector(self.btnDayChartPressed))
			btnTaskGraph.addGestureRecognizer(tapGesture)
			
			let longGesture = UILongPressGestureRecognizer(target: self, action:
				#selector(btnDayChartLongPressed))
			btnTaskGraph.addGestureRecognizer(longGesture)
			
			let projId = getProjectId(taskId: taskId!)
			// Setup project color.
			btnTaskGraph.backgroundColor = g_dictProjectDetails[projId]!.color
			btnTaskGraph.layer.masksToBounds = true
			btnTaskGraph.layer.cornerRadius = 3
			
			arrBtnsDayTask.append(btnTaskGraph)
			self.addSubview(arrBtnsDayTask.last!)
			
			let cgSize = CGSize(width: (endX-startX), height: 20)
			UIView.animate(withDuration: 0.5){
				self.arrBtnsDayTask.last!.frame.size = cgSize
			}
			i += 1
			
			// Update graph height.
			let maxHeight = max(nsLBarViewHeight.constant, CGFloat(110 + (88 - minY)))
			nsLBarViewHeight.constant = maxHeight
			
			//Set graph positiion
			viewGraphXAxis.frame.origin = CGPoint(x: 0, y: maxHeight - 20)
		}
		// Set btn positions.
		let steps = (nsLBarViewHeight.constant - 110)
		for btn in arrBtnsDayTask {
			var minYBtn = btn.frame.minY
			let minXBtn = btn.frame.minX
			minYBtn += steps
			btn.frame.origin = CGPoint(x: minXBtn, y: minYBtn)
		}
	}
	
	/// Dragged to button position.
	@objc func btnDayChartLongPressed(sender: UIGestureRecognizer) {
		if sender.state == .began {
			if let button = sender.view as? UIButton {
				setUpLabelForStartAndEndTime(btn: button)
			}
		}
		if sender.state == .ended || sender.state == .cancelled || sender.state == .failed {
			lblStartEndTime.isHidden = true
			// Remove drawn lines
			_ = layer.sublayers?.popLast()
		}
	}
	
	/// Button chart view pressed.
	@objc func btnDayChartPressed(sender: UIGestureRecognizer) {
		// If any button highlighted then, remove highlight.
		if let indexPath = tblActivities.indexPathForSelectedRow {
			removeHighlightDayGraphButton(index: indexPath.row)
			
			if let cell = tblActivities.cellForRow(at: indexPath) {
//				cell?.contentView.backgroundColor = g_colorMode.defaultColor()
				(cell as! UserTaskInfoCell).gradientLayer.colors =
					[]
			}
			cgPBtnDayGraphWidth = nil
		}
		if let button = sender.view as? UIButton {
			self.bringSubviewToFront(button)
			highlightDayGraphButton(index: button.tag)
			let indexPath = IndexPath(row: button.tag, section: 0)
			
			UIView.animate(withDuration: 0.3, animations: {
				self.tblActivities.selectRow(at: indexPath, animated: false, scrollPosition: .middle)
			}) {
				_ in
				let cell = self.tblActivities.cellForRow(at: indexPath) as! UserTaskInfoCell
//				cell?.contentView.backgroundColor = UIColor.lightGray.withAlphaComponent(0.5)
				cell.gradientLayer.colors = [UIColor.lightGray
					.withAlphaComponent(0.1).cgColor, UIColor.lightGray
						.withAlphaComponent(0.3).cgColor]
			}
		}
	}
	
	func setUpLabelForStartAndEndTime(btn: UIButton) {
		let pointX = btn.frame.midX
		var pointY = btn.frame.minY
		
		// Draw line
		var point = CGPoint(x: pointX, y: pointY)
		let line = CAShapeLayer()
		let linePath = UIBezierPath()
		linePath.move(to: point)
		
		let nLines = Int((btn.frame.minY - barChartView.frame.minY) / 5)
		for i in 0..<nLines {
			pointY -= 5
			point = CGPoint(x: pointX, y: pointY)
			if i%2 == 0 {
				// Draw line
				linePath.addLine(to: point)
			}
			else {
				// else provide gap
				linePath.move(to: point)
			}
		}
		// Draw triangle
		let pointLeft = CGPoint(x: point.x - 5, y: point.y - 5)
		linePath.addLine(to: pointLeft)
		let pointRight = CGPoint(x: point.x + 5, y: point.y - 5)
		linePath.addLine(to: pointRight)
		linePath.addLine(to: point)
		
		// Render to view.
		line.path = linePath.cgPath
		line.strokeColor = g_colorMode.lineColor().cgColor
		line.lineWidth = 2
		line.lineJoin = CAShapeLayerLineJoin.round
		line.fillColor = g_colorMode.lineColor().cgColor
		layer.addSublayer(line)
		
		// Apply animation while drawing.
		CATransaction.begin() //Begin CATransaction.
		let animation = CABasicAnimation(keyPath: "strokeEnd")
		animation.duration = 0.2
		animation.fromValue = 0
		animation.toValue = 1
		animation.timingFunction = CAMediaTimingFunction(name:
			CAMediaTimingFunctionName.linear)
		line.strokeEnd = 1.0
		
		// Callback function
		CATransaction.setCompletionBlock {
			// Show label after completion of drawing.
			// Setup label center.
			let cgRect = CGRect(x: pointX - 75, y: pointY-5, width: 150, height: 0)
			self.lblStartEndTime.frame = cgRect
			self.lblStartEndTime.isHidden = false
			
			// Animation while showing label
			UIView.animate(withDuration: 0.1) {
				let cgSize = CGSize(width: 150, height: -20)
				self.lblStartEndTime.frame.size = cgSize
			}
		}
		line.add(animation, forKey: "animateLine")
		CATransaction.commit()
		
		let cTaskDetails = arrCTaskTimeDetails[btn.tag]
		let startTime = getSecondsToHoursMinutesSecondsWithAllFields(seconds:
			cTaskDetails.nStartTime)
		let endTime = getSecondsToHoursMinutesSeconds(seconds: cTaskDetails.nEndTime)
		
		let strStart = convert24to12Format(strTime: startTime)
		let strEnd = convert24to12Format(strTime: endTime)
		
		lblStartEndTime.text = "\(strStart) - \(strEnd)"
	}

	/// Set up day activity view.
	func setupDayView() {
        // Setup tableview for a day.
		// Set header info view height.
		nsLBarViewHeight.constant = 110
		for btn in arrBtnsDayTask {
			btn.removeFromSuperview()
		}
		arrBtnsDayTask.removeAll()
		arrIntDate = tasksTimeCDCtrlr.getAllDates() // get all dates task timings.
		
		if arrIntDate.count > 0 && arrIntDate.count > indexSelDate {
			arrCTaskTimeDetails = tasksCDCtrlr.getEachTaskTimeDataFromDate(intDate:
				arrIntDate[indexSelDate])
			nCell = arrCTaskTimeDetails.count
			tblActivities.reloadDataWithAnimation()
			drawDayDetailsGraph()
			
			let strDate = Date().getStrDate(from: arrIntDate[indexSelDate]) // Initial setup for current date.
			lblDate.text = getDayWeekMonthInString(strDate: strDate)
			
			// Get total time excluding overlaped tasks time.
			let totalTime = tasksTimeCDCtrlr.getTotalWorkTime(intDate: arrIntDate[indexSelDate])
			lblTotalHr.text = """
			Duration: \(getSecondsToHoursMinutesSeconds(seconds: totalTime))
			"""
		}
		else {
			lblDate.text = getCurrentDate()
			lblTotalHr.text = """
			Duration: 00:00:00
			"""
		}
		checkArrowAlpha()
    }
	
	/// Setup week activity view.
	func setupWeekView() {
		arrWeekDetails = tasksCDCtrlr.getWeekWiseDetails() // Get week information.
		arrIntDate = tasksTimeCDCtrlr.getAllDates() // get all dates task timings.
		if arrWeekDetails.count > 0 {
			// Setup tableview.
//			arrWeekDetails = tasksCDCtrlr.getWeekWiseDetails()
			let weekDetails = arrWeekDetails[nWeek]
			arrIntDate = weekDetails.arrDates // Value in timesince1970
			arrCTaskDetails = tasksCDCtrlr.getDataFromDate(arrDate: arrIntDate)
			nCell = arrCTaskDetails.count
			tblActivities.reloadDataWithAnimation()
			
			// Display week information
			let strTotWork = getSecondsToHoursMinutesSeconds(seconds: weekDetails.totalWork)
			lblTotalHr.text = "Duration: \(strTotWork)"
			let week = weekDetails.weeknumber
			lblDate.text = getStartAndEndDateFromWeekNumber(weekOfYear: week!)
			
			for intDate in arrIntDate {
				// Draw button for all seven days in a view based on total work time in a day.
				let strDate = Date().getStrDate(from: intDate)
				let totalWork = CGFloat(tasksTimeCDCtrlr.getTotalWorkTime(intDate: intDate))
				let day = getDayNumber(strDate: strDate)
					
				// Get ratio of each project work.
				let dictRatio = tasksTimeCDCtrlr.getTaskRatioBasedOnProject(intDate: intDate)
				// Sort project id's
				let sortedProjId = Array(dictRatio.keys).sorted(by: <)
				
				// Find ratio and colors based on projects.
				var colors: [UIColor] = []
				var ratio: [CGFloat] = []
				for projId in sortedProjId {
					colors.append(g_dictProjectDetails[projId]!.color)
					ratio.append(dictRatio[projId]!)
				}
				arrBtnWeekView[day-1].colors = colors
				arrBtnWeekView[day-1].values = ratio
				
				// day-1 starts from sunday
				arrBtnWeekView[day-1].setTitle(String(intDate), for: .normal)
				let diff = ((UIScreen.main.bounds.height * 0.25) / 6) * 4
				
				// Height calculated based on total work time. (43200sec = 12hour)
				// value 70 : Drawing x axis labels height and upper remaining heights.
				let height: CGFloat = CGFloat(totalWork * diff) / CGFloat(43200)
				// Animate frame setup.
				UIView.animate(withDuration: 0.5) {
					self.arrBtnWeekView[day-1].height = -height
				}
			}
		}
		else {
			// If no work in a week
			let totTime = tasksTimeCDCtrlr.getTotalWorkTime(intDate: Date().millisecondsSince1970)
			let strTotWork = "\(getSecondsToHoursMinutesSeconds(seconds: totTime))"
			lblTotalHr.text = "Duration: \(strTotWork)"
			let week = getWeekNumber(nDate: Date().millisecondsSince1970)
			lblDate.text = getStartAndEndDateFromWeekNumber(weekOfYear: week)
		}
		checkArrowAlpha()
	}
    
    func updateChartWithData() {
		// If day view.
        if nSliderView == 0 {
			// Draw a x axis values from following constraints.
			let frameView = UIScreen.main.bounds
			let startPoint = CGPoint(x: 0, y: 5)
			let endPoint = CGPoint(x: frameView.maxX-40 , y: 5)
			let cgRect = CGRect(x: 0, y: 80, width: frameView.maxX-40, height: 20)
			viewGraphXAxis = UIView(frame: cgRect)
			viewGraphXAxis.isHidden = true
			viewGraphXAxis.backgroundColor = g_colorMode.defaultColor()
			viewGraphXAxis.drawXAxisForDay(start: startPoint, toPoint: endPoint, ofColor: .lightGray,
												lineWidth: 1.0)
			barChartView.addSubview(viewGraphXAxis)
			
			arrBtnsDayTask = []
			calendarView.isHidden = true  // Hide calendar view.
			setupDayView()
        }
		// If week view.
        else if nSliderView == 1 {
			// Set header info height to 35% of screen height.
			nsLBarViewHeight.constant = UIScreen.main.bounds.height * 0.25
			// Draw a x axis values from following constraints.
			let frameView = UIScreen.main.bounds
			let startPoint = CGPoint(x: 0, y: frameView.height * 0.25)
			let endPoint = CGPoint(x: frameView.maxX-40 , y: frameView.height * 0.25)
			barChartView.drawXAxisForWeek(start: startPoint, toPoint: endPoint, ofColor: .lightGray,
										  lineWidth: 1.0)
			
			arrWeekDetails = tasksCDCtrlr.getWeekWiseDetails() // Get week information.
			arrBtnWeekView = []
			
			let width = endPoint.x - startPoint.x // Total width of x-axis.
			let gap: CGFloat = width / 7 // Gap required for 7 days.
			cgFButtonMinY = startPoint.y + 30
			// Create 7 buttons: Represents 7 days total work time based on button height.
			for i in 0..<7 {
				let x = gap * CGFloat(i+1) - (gap / 2) // Calculate x value for a button.
				let frame = CGRect(x: x + 10, y: startPoint.y + 30, width: 20, height: 0)
				let btn = ButtonWeekGraph(frame: frame)
//				btn.backgroundColor = g_colorMode.midColor()
				btn.tag = i
				btn.topRounded()
				btn.setTitleColor(.clear, for: .normal)
				btn.addTarget(self, action:#selector(self.btnChartBarPressed), for: .touchUpInside)
				arrBtnWeekView.append(btn)
				self.addSubview(arrBtnWeekView[i])
			}
			calendarView.isHidden = true
		}
		else {
			calendarView.dataSource = self
			calendarView.delegate = self
			calendarView.setDisplayDate(Date()) // Initially display current month.
			dateCurrentMonth = calendarView.displayDate
			calendarView.marksWeekends = false
			let strMonth = calendarView.dateOnHeader(dateCurrentMonth)
			lblDate.text = strMonth
			nSelectedIndexMonth = 0
			calendarView.backgroundColor = .clear
			self.updateMonthDataSource()
			self.setupMonthView()
		}
    }
	
	/// To update month data source.
	func updateMonthDataSource() {
		arrDictMonthData = tasksCDCtrlr.getMonthWiseDetails()
		for monthDetails in arrDictMonthData {
			// arrDate contains all working days date in month.
			let arrDate = monthDetails.arrStrDates
			for strDate in arrDate {
				// Returns date object.
				let date = getDateFromString(strDate: strDate)
				// date will be selected. Total work of each day represented in circle.
				calendarView.selectDate(date)
			}
		}
	}
	
	// Setup month view.
	func setupMonthView() {
		if arrDictMonthData.count > 0 && arrDictMonthData.count > nSelectedIndexMonth
			, let mothDetails = arrDictMonthData?[nSelectedIndexMonth] {
			// Display inforamation about a month.
			let arrIntDate = mothDetails.arrDates
			let totWork = mothDetails.totalWork
			let strTotWork = getSecondsToHoursMinutesSeconds(seconds: totWork)
			lblTotalHr.text = "Duration: \(strTotWork)"
			arrCTaskDetails = tasksCDCtrlr.getDataFromDate(arrDate: arrIntDate)
			nCell = arrCTaskDetails.count
			tblActivities.reloadDataWithAnimation()
		}
		else {
			lblTotalHr.text = "Duration: 00:00:00"
		}
		checkArrowAlpha()
		calendarView.reloadData()
	}
	
	func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
//		var taskName: String!
//		if nSliderView == 0 {
//			let cTaskTimeDetails = arrCTaskTimeDetails[indexPath.row]
//			let taskId = cTaskTimeDetails.taskId!
//			taskName = tasksCDCtrlr.getTaskName(taskId: taskId)
//		}
//		else {
//			let cTaskTimeDetails = arrCTaskDetails[indexPath.row]
//			let taskId = cTaskTimeDetails.taskId!
//			taskName = tasksCDCtrlr.getTaskName(taskId: taskId)
//		}
		
		// Check height for label. (To increase cell height)
//        let label = UILabel(frame: CGRect(x: 0, y: 0, width: UIScreen.main.bounds.width - 57,
//										  height: 20))
//        label.numberOfLines = 0
//        label.lineBreakMode = .byWordWrapping
//        label.text = taskName
//        label.sizeToFit()
//        return 100 + label.bounds.height
		
		return 120
    }
	
	/// To remove previously highlighted button.
	func removeHighlightDayGraphButton(index: Int) {
		if let cgFrame = cgPBtnDayGraphWidth {
			// Negative value because its in reverse order.
			let btnDayGraph = arrBtnsDayTask[arrCTaskTimeDetails.count - index - 1]
			let color = btnDayGraph.backgroundColor
			UIView.animate(withDuration: 0.2) {
				btnDayGraph.frame = cgFrame
				btnDayGraph.backgroundColor = color?.withAlphaComponent(1.0)
			}
		}
	}
	
	func highlightDayGraphButton(index: Int) {
		// Negative value because its in reverse order.
		let btnDayGraph = arrBtnsDayTask[arrCTaskTimeDetails.count - index - 1]

		// To avoid multiple click on same cell.
		if btnDayGraph.frame.height != 44 {
			cgPBtnDayGraphWidth = btnDayGraph.frame
			let maxWidth = max(44, btnDayGraph.frame.width)
			var minX = btnDayGraph.frame.minX
			
			// If button width too less.
			if btnDayGraph.frame.width <= 32 {
				minX -= 12
			}
			let minY = btnDayGraph.frame.minY - 12
			let cgFrame = CGRect(x: minX, y: minY, width: maxWidth, height: 44)
			let color = btnDayGraph.backgroundColor
			
			bringSubviewToFront(btnDayGraph)
			UIView.animate(withDuration: 0.2, animations: {
				btnDayGraph.frame = cgFrame
				btnDayGraph.backgroundColor = color?.withAlphaComponent(1.0)
				
			}, completion: { _ in
				self.removeHighlightDayGraphButton(index: index)
			})
		}
	}
	
	func tableView(_ tableView: UITableView, canEditRowAt indexPath: IndexPath) -> Bool {
		// Without puchin updation no edit option, as well as after punched out.
//		if !(g_isPunchedIn ?? false) || (g_isPunchedOut) {
//			return false
//		}
		return true
	}
	
	func tableView(_ tableView: UITableView, editActionsForRowAt indexPath: IndexPath) ->
		[UITableViewRowAction]? {
			var taskId: Int!
			if nSliderView == 0 {
				let cTaskTimeDetails = self.arrCTaskTimeDetails[indexPath.row]
				taskId = cTaskTimeDetails.taskId!
			}
			else {
				// Tap to move edit page.
				let cTaskTimeDetails = arrCTaskDetails[indexPath.row]
				taskId = cTaskTimeDetails.taskId!
				
			}
			
			// Setup stop action.
			let stopAction = UITableViewRowAction(style: .default, title: "Stop" , handler: {
				(action:UITableViewRowAction, indexPath: IndexPath) -> Void in
				self.delegate?.cellSwipeToStop(taskId: taskId)
			})
			stopAction.backgroundColor =  g_colorMode.midColor()
			
			// If punched out.
//			if g_isPunchedOut {
//				return nil
//			}
			
			// If task is running guard it.
			let taskInfo = getTaskDetails(taskId: taskId)
			if true == taskInfo?.bIsRunning {
				return [stopAction]
			}
			
			let editAction = UITableViewRowAction(style: .default, title: "Edit" , handler: {
				(action:UITableViewRowAction, indexPath: IndexPath) -> Void in
				self.delegate?.cellSelected(taskId: taskId)
			})
			editAction.backgroundColor = g_colorMode.midColor()
			return [editAction]
	}
	
	func tableView(_ tableView: UITableView, didHighlightRowAt indexPath: IndexPath) {
		if let cell = tableView.cellForRow(at: indexPath) as? UserTaskInfoCell {
			// Highlight touch.
			cell.gradientLayer.colors = [g_colorMode.textColor().withAlphaComponent(0.2).cgColor
				, g_colorMode.textColor().withAlphaComponent(0.2).cgColor]
		}
	}
	
	func tableView(_ tableView: UITableView, didUnhighlightRowAt indexPath: IndexPath) {
		tableView.reloadRows(at: [indexPath], with: .none)
	}
	
	func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
		if nSliderView == 0 {
			// Beacause sorting different for table view and grapgh.
			highlightDayGraphButton(index: indexPath.row)
		}
		else {
		}
	}
	
	func tableView(_ tableView: UITableView, didDeselectRowAt indexPath: IndexPath) {
		if nSliderView == 0 {
		}
	}
 
	@objc func btnChartBarPressed(sender: UIButton!) {
		let intDate = Int64(sender.titleLabel!.text!)
		let strDate = Date().getStrDate(from: intDate!)
		// Date indicates start time of day.
		let date = getDateFromString(strDate: strDate)
		let nDate = date.millisecondsSince1970
		delegateChart?.chartPressed(intDate: nDate)
	}
    
    required init?(coder aDecoder: NSCoder) {
       super.init(coder: aDecoder)
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
		// To show and hide data availability in month view.
		if nSliderView == 2 && nCell == 0 {
			lblNoData.isHidden = false
		}
		else {
			lblNoData.isHidden = true
		}
		
		// In initial user creation condition if task count is zero.
		if arrIntDate.count == 0 && nSliderView != 2 {
			lblNoData.isHidden = false
		}
		else if nSliderView != 2 {
			lblNoData.isHidden = true
		}
        return nCell
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
		let cell = tableView.dequeueReusableCell(withIdentifier: "userBreakInfoCell",
												 for: indexPath) as! UserTaskInfoCell
		cell.imgTimer.image = #imageLiteral(resourceName: "timer3")
		cell.imgTimer.alpha = 0.2
		if nSliderView == 0 {
			let cTaskTimeDetails = arrCTaskTimeDetails[indexPath.row]
			let taskId = cTaskTimeDetails.taskId
			let cTaskDetails = tasksCDCtrlr.getDetails(taskId: taskId!)!
			cell.lblTotalDuration.text =
			"\(getSecondsToHoursMinutesSeconds(seconds: cTaskTimeDetails.nTotalTime))"
			let strTime = getSecondsToHoursMinutesSecondsWithAllFields(seconds:
				cTaskTimeDetails.nStartTime)
			let strDate = cTaskTimeDetails.strDate
			cell.lblStartTime.text =
				"\(getDateDay(date: strDate!)) \(convert24to12Format(strTime: strTime))"
			cell.lblTaskName.text = "\(cTaskDetails.taskName!)"
			let projId = cTaskDetails.projId!
			cell.lblProjectName.text = "\(g_dictProjectDetails[projId]!.projName!)"
			cell.ntaskId = indexPath.row + 1
			// Update project icon.
			
			let cProjectDetails = g_dictProjectDetails[projId]!
			cell.lblCategory.backgroundColor = cProjectDetails.color
			if let img = cProjectDetails.imgProjIcon {
				cell.imgVProjectIcon.image = img
			}
			else {
				downloadImage(from: g_dictProjectDetails[projId]!.urlProjIcon,
							  imgView: cell.imgVProjectIcon)
			}
		}
		else {
			let cTaskDetails = arrCTaskDetails[indexPath.row]
			cell.lblTotalDuration.text =
			"\(getSecondsToHoursMinutesSeconds(seconds: cTaskDetails.nTotalTime!))"
			let strTime = getSecondsToHoursMinutesSecondsWithAllFields(seconds:
				cTaskDetails.getStartTime()!)
			if let strDate = cTaskDetails.getStartDate() {
				if let _ = cTaskDetails.nEndTime  {
					let strEndTime =
						getSecondsToHoursMinutesSeconds(seconds: cTaskDetails.getEndTime()!)
					let endStrDate = cTaskDetails.getEndDate()!
					cell.lblStartTime.text =
					"\(getDateDay(date: strDate)) \(convert24to12Format(strTime: strTime)) - \(getDateDay(date: endStrDate)) \(convert24to12Format(strTime: strEndTime))"
					cell.lblStartTime.adjustsFontSizeToFitWidth = true
					cell.imgTimer.image = #imageLiteral(resourceName: "successIcon")
					cell.imgTimer.alpha = 1.0
				}
				else {
					cell.lblStartTime.text =
					"\(getDateDay(date: strDate)) \(convert24to12Format(strTime: strTime))"
				}
			}
			else {
				cell.lblStartTime.text = "Not Started"
			}
			cell.lblTaskName.text = "\(cTaskDetails.taskName!)"
			let projId = cTaskDetails.projId!
			cell.lblProjectName.text = "\(g_dictProjectDetails[projId]!.projName!)"
			cell.ntaskId = indexPath.row + 1
			// Update project icon.
			let cProjectDetails = g_dictProjectDetails[projId]!
			cell.lblCategory.backgroundColor = cProjectDetails.color
			if let img = cProjectDetails.imgProjIcon {
				cell.imgVProjectIcon.image = img
			}
			else {
				downloadImage(from: g_dictProjectDetails[projId]!.urlProjIcon,
							  imgView: cell.imgVProjectIcon)
			}
		}
		cell.selectionStyle = .none
		cell.contentView.backgroundColor = .clear
		return cell
	}
	
	@IBAction func btnLeftArrowPressed(_ sender: Any) {
		if nSliderView == 0 {
			// If day view changed to previous day.
			if indexSelDate < arrIntDate.count - 1 {
				indexSelDate += 1
				setupDayView()
			}
		}
		else if nSliderView == 1{
			// If week view changed to previous week.
			if nWeek < arrWeekDetails.count - 1 {
				nWeek += 1
				resetWeekBar()
				// Scroll table view to top.
				if arrCTaskDetails.count > 0 && tblActivities.contentOffset.y > 0 {
					tblActivities.scrollToRow(at: [0,0], at: .top, animated: true)
				}
				setupWeekView()
			}
		}
		else{
			// Move to previous month.
			calendarView.goToPreviousMonth()
		}
	}
	
	/// Setup left and right arrow button alpha value, based on data existance.
	func checkArrowAlpha() {
		btnLeftMove.alpha = 0.4
		btnRightMove.alpha = 0.4
		if nSliderView == 0 {
			if indexSelDate >= arrIntDate.count - 1 {
				btnLeftMove.alpha = 0.1
			}
			else {
				// Show intro pagein day view. (If first installation/reset intro page)
				if false == UserDefaults.standard.value(forKey: "IntroStatusDayLeft")
					as? Bool ?? false {
					delegate?.showIntroPageDayView()
				}
			}
			if indexSelDate == 0 {
				btnRightMove.alpha = 0.1
			}
		}
		else if nSliderView == 1 {
			if nWeek >= arrWeekDetails.count - 1 {
				btnLeftMove.alpha = 0.1
			}
			else {
				// Show intro page week view. (If first installation/reset intro page)
				if false == UserDefaults.standard.value(forKey: "IntroStatusWeekLeft")
					as? Bool ?? false {
					delegate?.showIntroPageWeekView()
				}
			}
			if nWeek <= 0 {
				btnRightMove.alpha = 0.1
			}
		}
		else {
			if nSelectedIndexMonth == 0 {
				btnRightMove.alpha = 0.1
			}
		}
	}
	
	@IBAction func btnRightArrowPressed(_ sender: Any) {
		if nSliderView == 0 {
			// If day view changed to next day.
			if indexSelDate > 0 {
				indexSelDate -= 1
				setupDayView()
			}
		}
		else if nSliderView == 1 {
			// If week view changed to next week.
			if nWeek > 0 {
				nWeek -= 1
				resetWeekBar()
				// Scroll to top.
				if arrCTaskDetails.count > 0 {
					tblActivities.scrollToRow(at: [0,0], at: .top, animated: true)
				}
				setupWeekView()
			}
		}
		else {
			// Move to next month.
			calendarView.goToNextMonth()
		}
	}
	
	/// Set button height to zero.
	func resetWeekBar() {
		for intDate in arrIntDate {
			let strDate = Date().getStrDate(from: intDate)
			let day = getDayNumber(strDate: strDate)
			let btn = arrBtnWeekView[day-1]
			btn.height = 0
			btn.y = cgFButtonMinY
		}
	}
}

extension UIView {
	// Draw x axis label for day view graph.
	func drawXAxisForDay(start : CGPoint, toPoint end:CGPoint, ofColor lineColor: UIColor,
                   lineWidth: CGFloat) {
		// Draw a line
		let path = UIBezierPath()
        path.move(to: start)
        path.addLine(to: end)
		
        let width = end.x - start.x
        let gap: CGFloat = width / 6 // Gap 6: from 8AM to 8PM(3hr gap)
        for i in 0..<7 {
			// Draw vertical line of height 4 (Divider).
            let x = gap * CGFloat(i)
            let startPoint = CGPoint(x: x, y: start.y)
            let endPoint = CGPoint(x: x, y: start.y + 4)
            path.move(to: startPoint)
            path.addLine(to: endPoint)
			
			// Setup label for time.
            let frame = CGRect(x: x - 15, y: start.y + 10, width: 30, height: 30)
            let label = UILabel (frame: frame)
            if i < 2 {
                label.text = "\(8 + i * 2)\nAM"
            }
            else if i == 2 {
                label.text = "12\nPM"
            }
            else {
                label.text = "\((i-2) * 2)\nPM"
            }
            label.font = label.font.withSize(12)
            label.numberOfLines = 2
            label.textColor = g_colorMode.lineColor()
            label.textAlignment = .center
            self.addSubview(label)
        }
        // Design path in layer
        let shapeLayer = CAShapeLayer()
        shapeLayer.path = path.cgPath
		shapeLayer.strokeColor = lineColor.cgColor.copy(alpha: 0.4)
        shapeLayer.lineWidth = lineWidth
        layer.addSublayer(shapeLayer)
    }
	
	/// draw x axis label to a week view.
	func drawXAxisForWeek(start : CGPoint, toPoint end:CGPoint, ofColor lineColor: UIColor,
					  lineWidth: CGFloat) {
		// Draw a line.
		let path = UIBezierPath()
        path.move(to: start)
        path.addLine(to: end)
		
		let gridPath = UIBezierPath()
		for i in 1...5 {
			// 4 horizontal lines.
//			let diff = self.bounds.height / 7 // Difference between horizontal line.
			let diff = UIScreen.main.bounds.height * 0.25 / 6 // Difference between horizontal line.
			let startPoint = CGPoint(x: start.x, y: start.y - CGFloat(i)*diff)
			let endPoint = CGPoint(x: end.x, y: start.y - CGFloat(i)*diff)
			
			// Create label for work timings.
			var frame = CGRect(x: end.x+2, y: endPoint.y - 5, width: 20, height: 10)
			
			// 5th postion to display y label header.
			if i == 5 {
				// Change y postion.
				frame = CGRect(x: end.x+2, y: endPoint.y + 10, width: 20, height: 10)
			}
			let label = UILabel (frame: frame)
			switch i {
				case 1 : label.text = "3"
					
				case 2 : label.text = "6"
					
				case 3: label.text = "9"
					
				case 4 : label.text = "12"
					
				case 5 : label.text = "hr"
				
				default: break
			}
			
			label.font = label.font.withSize(10)
            label.textColor = g_colorMode.lineColor()
            label.textAlignment = .left
            self.addSubview(label)
			
			// 5th postion to display y label header.
			if i != 5 {
				// Draw a horizontal lines represents hour.
				gridPath.move(to: startPoint)
				gridPath.addLine(to: endPoint)
			}
		}
		
		// Design path in layer
		let gridShapeLayer = CAShapeLayer()
        gridShapeLayer.path = gridPath.cgPath
        gridShapeLayer.strokeColor = lineColor.cgColor
		gridShapeLayer.lineWidth = 0.25
        layer.addSublayer(gridShapeLayer)

		let width = end.x - start.x
        let gap: CGFloat = width / 7
        
		// Draw x - axis values.
		for i in 0..<7 {
            let x = gap * CGFloat(i+1) - (gap / 2)
            let startPoint = CGPoint(x: x, y: start.y)
            let endPoint = CGPoint(x: x, y: start.y + 4)
			
			// Draw a line
            path.move(to: startPoint)
            path.addLine(to: endPoint)
            
			//  labels represents days.
            let frame = CGRect(x: x - 15, y: start.y + 10, width: 30, height: 15)
            let label = UILabel (frame: frame)
			switch  i {
				case 6: label.text = "Sun"
						break
				case 0: label.text = "Mon"
						break
				case 1: label.text = "Tue"
						break
				case 2: label.text = "Wed"
						break
				case 3: label.text = "Thu"
						break
				case 4: label.text = "Fri"
						break
				case 5: label.text = "Sat"
						break
				default: break
			}
            label.font = label.font.withSize(12)
            label.textColor = g_colorMode.lineColor()
            label.textAlignment = .center
            self.addSubview(label)
        }
        // design path in layer
        let shapeLayer = CAShapeLayer()
        shapeLayer.path = path.cgPath
        shapeLayer.strokeColor = lineColor.cgColor.copy(alpha: 0.4)
        shapeLayer.lineWidth = lineWidth
        layer.addSublayer(shapeLayer)
	}
    
	/// Draw day view graph.
    func drawDayGraph(start : CGPoint, toPoint end:CGPoint, arrDictVal: Dictionary<Int, [Int]>) {
		if layer.sublayers!.count > 10 {
			// Exist 10 layers representing x axis.
			// Remove all remaining layers.
			for _ in 10..<layer.sublayers!.count {
				layer.sublayers?.removeLast()
			}
		}
		
		for (id, array) in Array(arrDictVal).sorted(by: {$0.0 < $1.0}) {
            var i = 0
            for _ in 0..<array.count / 2 {
				// Calculation based on total work time from 8AM to 8PM
				// 28800sec = 8AM
				// 43200sec = 8PM
                let startVal = CGFloat(array[i] - 28800) * end.x / 43200
                let endVal = CGFloat(array[i+1] - 28800) * end.x / 43200
                let startPoint = CGPoint(x: startVal, y: start.y - 22)
                let endPoint = CGPoint(x: endVal, y: start.y - 22)

                let path = UIBezierPath()
                let shapeLayer = CAShapeLayer()

				// Draw a line.
                path.move(to: startPoint)
                path.addLine(to: endPoint)
				
                let taskCDController = TasksCDController()
                let projId = taskCDController.getProjectId(taskId: id)
				// Setup project color.
                if projId == 1 {
                    shapeLayer.strokeColor = UIColor.blue.cgColor
                }
                else if projId == 2 {
                    shapeLayer.strokeColor = UIColor.red.cgColor
                }
                else {
                    shapeLayer.strokeColor = UIColor.green.cgColor
                }
                shapeLayer.path = path.cgPath
                shapeLayer.lineWidth = 10
				layer.addSublayer(shapeLayer)
				
				// Apply animation while drawing.
				let animation = CABasicAnimation(keyPath: "strokeEnd")
				animation.duration = 0.4
				animation.fromValue = 0
				animation.toValue = 1
				animation.timingFunction = CAMediaTimingFunction(name:
					CAMediaTimingFunctionName.linear)
				shapeLayer.strokeEnd = 1.0
				shapeLayer.add(animation, forKey: "animateCircle")
				
				
				// Add following lines to differentiate breaks.
/*				let path = UIBezierPath()
				let shapeLayer = CAShapeLayer()
				startPoint = CGPoint(x: startVal, y: start.y - 42)
				endPoint = CGPoint(x: startVal, y: start.y - 42)

				path.move(to: startPoint)
				path.addLine(to: endPoint)

				shapeLayer.strokeColor = UIColor.white.cgColor
				shapeLayer.path = path.cgPath
				shapeLayer.lineWidth = 2
				layer.addSublayer(shapeLayer) */
                i += 2
            }
        }
		layer.zPosition = 1
    }
}
