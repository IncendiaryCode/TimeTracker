/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : GlobalValuesFunctions.swift
 //
 //    File Created      : 18:Sept:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : All global values and functions.
 //
 //////////////////////////////////////////////////////////////////////////// */

import Foundation
import UIKit
import CoreData

/// Base url of time tracker project API request (brightlightventures.com/timetracker/index.php/api).
let g_baseURL = "http://www.brightlightventures.com"
let g_subUrl = "/timetracker/index.php/api"
/// Request punch in/out details using page no.
var g_loginPageNo = 1
/// User profile picture.
var g_userProfile: UIImage!
/// Pagination number to task data loader api.
var g_taskPageNo = 1
/// pagination count for task.
var g_totalPagesTask: Int!
/// pagination count for project.
var g_totalPagesPunchInOut: Int!
/// Tasks in data one page.
var g_taskCountInPage = 10
/// Punch in out data in one page.
var g_punchInOutCountInPage = 10
/// Global color mode.
var g_colorMode: ColorMode!
/// Storing globally all project details... Dictionary value where
/// Key = Project ID, Value = ProjectDetails class object
var g_dictProjectDetails: Dictionary<Int, ProjectDetails>!
/// Store all unfinished tasks information.
var g_arrCTaskDetails: Array<TaskDetails>!
/// Variable stores whether punchout is updated or not.
var g_isPunchedOut: Bool {
    get {
        let punchInOutCDCtrlr = PunchInOutCDController()
        return punchInOutCDCtrlr.isTodayPunchedOut()
    }
    set {
    }
}
/// Variable indicates punched in or not.
var g_isPunchedIn: Bool?
/// Animation duration to top
var g_nAnimatnDuratn = 0.2

enum TimeFormat {
    case hm
    case HHmmss
}

enum ColorMode: Int {
    case auto = 0
    case light = 1
    case dark = 2
    
    /// Get start color(default start and black) .
    func startColor() -> CGColor {
        switch self {
            case .light: return UIColor(red: 100/255, green: 105/255, blue: 255/255,
                                        alpha: 1.0).cgColor
            case .dark: return UIColor.black.cgColor
            
            case .auto :
                if #available(iOS 12.0, *) {
                    switch UIScreen.main.traitCollection
                        .userInterfaceStyle {
                        case .light: return UIColor(red: 100/255, green: 105/255, blue: 255/255,
                                                    alpha: 1.0).cgColor
                        case .dark: return UIColor.black.cgColor
                        default:
                            return UIColor.clear.cgColor
                    }
                } else {
                    return UIColor.clear.cgColor
            }
        }
    }
    
    // Get end color.
    func endColor() -> CGColor {
        switch self {
            case .light: return UIColor(red: 240/255, green: 128/255, blue: 245/255,
                                 alpha: 1.0).cgColor
            case .dark: return UIColor.darkGray.cgColor
            
            case .auto :
                if #available(iOS 12.0, *) {
                    switch UIScreen.main.traitCollection
                        .userInterfaceStyle {
                        case .light: return UIColor(red: 240/255, green: 128/255, blue: 245/255,
                                                    alpha: 1.0).cgColor
                        case .dark: return UIColor.darkGray.cgColor
                        default:
                            return UIColor.clear.cgColor
                    }
                } else {
                    return UIColor.clear.cgColor
            }
        }
    }
    
    // Get viewBackground color.
    func backgroundColor() -> UIColor {
        switch self {
            case .light: return UIColor(hexString: "#F1F2F6")!
            case .dark: return UIColor(hexString: "#0E0D09")!
            
            case .auto :
                if #available(iOS 12.0, *) {
                    switch UIScreen.main.traitCollection
                        .userInterfaceStyle {
                        case .light: return UIColor(hexString: "#F1F2F6")!
                        case .dark: return UIColor(hexString: "#0E0D09")!
                        default:
                            return .clear
                    }
                } else {
                    return .clear
            }
        }
    }
    
    // Get middle color.
    func midColor() -> UIColor {
        switch self {
            case .light: return  UIColor(red: 181/255, green: 108/255,
                                         blue: 249/255, alpha: 1.0)
            case .dark: return UIColor.gray
            
            case .auto :
                if #available(iOS 12.0, *) {
                    switch UIScreen.main.traitCollection
                        .userInterfaceStyle {
                        case .light: return  UIColor(red: 181/255, green: 108/255,
                                                     blue: 249/255, alpha: 1.0)
                        case .dark: return UIColor.gray
                        default:
                            return .clear
                    }
                } else {
                    return .clear
            }
        }
    }
    
    // Get text color. (light: gray, dark: white)
    func textColor() -> UIColor {
        switch self {
            case .light: return  .darkGray
            case .dark: return .white
            
            case .auto :
                if #available(iOS 12.0, *) {
                    switch UIScreen.main.traitCollection
                        .userInterfaceStyle {
                        case .light: return  .darkGray
                        case .dark: return .white
                        default:
                            return .clear
                    }
                } else {
                    return .clear
            }
        }
    }
    
    /// Get invert color. (black and white)
    func invertColor() -> UIColor {
        switch self {
            case .light: return  .black
            case .dark: return .white
            
            case .auto :
                if #available(iOS 12.0, *) {
                    switch UIScreen.main.traitCollection
                        .userInterfaceStyle {
                        case .light: return  .black
                        case .dark: return .white
                        default:
                            return .clear
                    }
                } else {
                    return .clear
            }
        }
    }
    
    /// Get default color.(white and black)
    func defaultColor() -> UIColor {
        switch self {
            case .light: return  .white
            case .dark: return .black
            
            case .auto :
                if #available(iOS 12.0, *) {
                    switch UIScreen.main.traitCollection
                        .userInterfaceStyle {
                        case .light: return  .white
                        case .dark: return .black
                        default:
                            return .clear
                    }
                } else {
                    return .clear
            }
        }
    }
    
    /// Get default color.(white and black)
    func switchColor() -> UIColor {
        switch self {
            case .light: return  .white
            case .dark: return UIColor(red: 181/255, green: 108/255,
                                       blue: 249/255, alpha: 1.0)
            
            case .auto :
                if #available(iOS 12.0, *) {
                    switch UIScreen.main.traitCollection
                        .userInterfaceStyle {
                        case .light: return  .white
                        case .dark: return UIColor(red: 181/255, green: 108/255,
                                                   blue: 249/255, alpha: 1.0)
                        default:
                            return .clear
                    }
                } else {
                    return .clear
            }
        }
    }
    
    /// Get Tint color.(app start color and white)
    func tintColor() -> UIColor {
        switch self {
            case .light: return UIColor(red: 181/255, green: 108/255,
                                        blue: 249/255, alpha: 1.0)
            case .dark: return .white
            
            case .auto :
                if #available(iOS 12.0, *) {
                    switch UIScreen.main.traitCollection
                        .userInterfaceStyle {
                        case .light: return  UIColor(red: 181/255, green: 108/255,
                                                     blue: 249/255, alpha: 1.0)
                        case .dark: return .white
                        default:
                            return .clear
                    }
                } else {
                    return .clear
            }
        }
    }
        
    /// Get text color.
    func btnbackgroundColor() -> UIColor {
        switch self {
            case .light: return  .white
            case .dark: return .lightGray
            
            case .auto :
                if #available(iOS 12.0, *) {
                    switch UIScreen.main.traitCollection
                        .userInterfaceStyle {
                        case .light: return  .white
                        case .dark: return .lightGray
                        default:
                            return .clear
                    }
                } else {
                    return .clear
            }
        }
    }
    
    /// Get line/text color().
    func lineColor() -> UIColor {
        switch self {
            case .light: return  .gray
            case .dark: return .lightGray
            
            case .auto :
                if #available(iOS 12.0, *) {
                    switch UIScreen.main.traitCollection
                        .userInterfaceStyle {
                        case .light: return  .gray
                        case .dark: return .lightGray
                        default:
                            return .clear
                    }
                } else {
                    return .clear
            }
        }
    }
    
    /// color(light gray with 0.8 opacity and white with 0.2 alpha)
    func midText() -> UIColor {
        switch self {
            case .light: return  UIColor.lightGray.withAlphaComponent(0.8)
            case .dark: return UIColor.white.withAlphaComponent(0.3)
            
            case .auto :
                if #available(iOS 12.0, *) {
                    switch UIScreen.main.traitCollection
                        .userInterfaceStyle {
                        case .light: return  UIColor.lightGray.withAlphaComponent(0.8)
                        case .dark: return UIColor.white.withAlphaComponent(0.2)
                        default:
                            return .clear
                    }
                } else {
                    return .clear
            }
        }
    }
    
    /// color(light white with 0.3 opacity and white with 0.3 alpha)
    func placeHolderColor() -> UIColor {
        switch self {
            case .light: return  UIColor.white.withAlphaComponent(0.3)
            case .dark: return UIColor.white.withAlphaComponent(0.3)
            
            case .auto :
                if #available(iOS 12.0, *) {
                    switch UIScreen.main.traitCollection
                        .userInterfaceStyle {
                        case .light: return  UIColor.white.withAlphaComponent(0.3)
                        case .dark: return UIColor.white.withAlphaComponent(0.3)
                        default:
                            return .clear
                    }
                } else {
                    return .clear
            }
        }
    }
    
    /// Alpha value (0.4 and 0.8)
    func alphaValueHigh() -> CGFloat {
        switch self {
            case .light: return  0.4
            case .dark: return 0.8
            
            case .auto :
                if #available(iOS 12.0, *) {
                    switch UIScreen.main.traitCollection
                        .userInterfaceStyle {
                        case .light: return  0.4
                        case .dark: return 0.8
                        default:
                            return 0.8
                    }
                } else {
                    return 0.8
            }
        }
    }
    
    /// color(light gray with 0.8 opacity and white with 0.2 alpha)
    func alphaValueLow() -> CGFloat {
        switch self {
            case .light: return  0.1
            case .dark: return 0.4
            
            case .auto :
                if #available(iOS 12.0, *) {
                    switch UIScreen.main.traitCollection
                        .userInterfaceStyle {
                        case .light: return  0.1
                        case .dark: return 0.4
                        default:
                            return 0.4
                    }
                } else {
                    return 0.4
            }
        }
    }
}

/// Set up app config
func setAppConfig() {
    // If first time installed.
    if nil == UserDefaults.standard.object(forKey: "multi_task") {
        UserDefaults.standard.set(true, forKey: "multi_task")
    }
    if nil == UserDefaults.standard.value(forKey: "colorMode") {
        // Initially set to light mode.
        UserDefaults.standard.setValue(1, forKey: "colorMode")
        g_colorMode = ColorMode.light
    }
}

/// Call whenever color changed.
func setColorMode() {
    if let index = UserDefaults.standard.value(forKey: "colorMode") {
        g_colorMode = ColorMode(rawValue: index as! Int)
        UIApplication.shared.windows.forEach { window in
            if #available(iOS 13.0, *) {
                switch g_colorMode {
                    case .light:
                        window.overrideUserInterfaceStyle = .light
                    
                    case .dark :
                        window.overrideUserInterfaceStyle = .dark
                
                    case .auto :
                        window.overrideUserInterfaceStyle = UIScreen.main.traitCollection
                            .userInterfaceStyle
                    
                    default:
                        window.overrideUserInterfaceStyle = .unspecified
                }
            }
        }
    }
}

/// Updates dictionary projectdetails
/// Call when log in is success
func updateProjectDetails() {
    let container = NSPersistentContainer(name: "UserTaskDetails")
    print(container.persistentStoreDescriptions.first?.url as Any)
    let projectCDCtrlr = ProjectsCDController()
    g_dictProjectDetails = projectCDCtrlr.getAllProjectDetails()
}

/// Get project name.
func getProjectName(projId: Int) -> String {
    let projDetails = g_dictProjectDetails[projId]!
    return projDetails.projName
}

/// Get project id from task id.
func getProjectId(taskId: Int) -> Int {
    let index = g_arrCTaskDetails.firstIndex(where: { (item) -> Bool in
        item.taskId == taskId
    })
    let projId = g_arrCTaskDetails[index!].projId
    return projId!
}

/// returns all project names.
func getAllProjectNames() -> Array<String> {
    var array = Array<String>()
    for (_, cTaskDetails) in g_dictProjectDetails {
        array.append(cTaskDetails.projName)
    }
    return array
}

func getAllProjectIds() -> Array<Int> {
    var array = Array<Int>()
    for (id, _) in g_dictProjectDetails {
        array.append(id)
    }
    return array
}

func getProjectNameAndIconUrl() -> Dictionary<String, UIImage> {
    var dictionary = Dictionary<String, UIImage>()
    for (_, cTaskDetails) in g_dictProjectDetails {
        dictionary.updateValue(cTaskDetails.imgProjIcon!, forKey: cTaskDetails.projName)
    }
    return dictionary
}

func getProjectId(projectName: String) -> Int? {
    for (id, cProjectDetails) in g_dictProjectDetails {
        if cProjectDetails.projName == projectName {
            return id
        }
    }
    return nil
}

/// sorting type in task details 
enum SortTypes {
    case projects
    case tasks
    case duration
    case none
}

class ProjectDetails{
    /// Project ID.
    var projId: Int!
    /// Project name.
    var projName: String!
    /// Project Logo url.
    var urlProjIcon: URL!
    /// Project Icon.
    var imgProjIcon: UIImage?
    /// Project color.
    var color: UIColor!
    /// Modules array (key = module id, value = module name)
    var dictModules: Dictionary<Int, String>!
    
    init(projId: Int, projName: String, url: URL, dictMod: Dictionary<Int, String>, color:
        UIColor) {
        self.projId = projId
        self.projName = projName
        self.urlProjIcon = url
        self.dictModules = dictMod
        self.color = color
        // To add image.
        downloadImage(from: url, cProjectDetails: self)
    }
}

class TaskDetails {
    /// Task ID.
    var taskId: Int!
    /// Task name
    var taskName: String!
    /// Task Description
    var taskDescription: String?
    /// Project ID
    var projId: Int!
    /// Module ID
    var moduleId: Int!
    /// Total time
    var nTotalTime: Int!
    /// Start time in time since 1970
    var nStartTime: Int64?
    /// End time in time since 1970
    var nEndTime: Int64?
    /// Page no provided by API. (Usefull when re-fetch task data)
    var pageNo: Int!
    /// Is running.
    var bIsRunning: Bool?
    /// Total task timings.
    lazy var arrTaskTimings: Array<TaskTimeDetails> = {
        let taskTimeCDController = TasksTimeCDController()
        let arrTaskTimeDetails: Array<TaskTimeDetails> =
            taskTimeCDController.getTaskTimes(taskId: self.taskId!)
        return arrTaskTimeDetails
    }()
    
    init(taskId: Int, taskName: String, taskDescr: String?, projId: Int, modId: Int, nTotalTime:
        Int, nStartTime: Int64?, nEndTime: Int64?, isRunnung: Bool?, pageNo: Int) {
        self.taskId = taskId
        self.taskName = taskName
        if let strTaskDescr = taskDescr {
            self.taskDescription = strTaskDescr
        }
        self.projId = projId
        self.moduleId = modId
        self.nTotalTime = nTotalTime
        self.pageNo = pageNo
        if 0 != nStartTime {
            self.nStartTime = nStartTime!
        }
        if 0 != nEndTime {
            self.nEndTime = nEndTime
        }
        if let bRunning = isRunnung {
            self.bIsRunning = bRunning
        }
    }
    
    /// Get task start time in seconds.
    func getStartTime() -> Int? {
        if let strartTime = self.nStartTime {
            let startDateTime = Date(milliseconds: strartTime)
            let nStartTime = startDateTime.timeInDate
            return nStartTime
        }
        else {
            return nil
        }
    }
    
    /// Get task end time in seconds.
    func getEndTime() -> Int? {
        if let endTime = self.nEndTime {
            let endDateTime = Date(milliseconds: endTime)
            let nEndTime = endDateTime.timeInDate
            return nEndTime
        }
        else {
            return nil
        }
    }
    
    /// Get Start date in string.
    func getStartDate() -> String? {
        if let startDate = self.nStartTime {
            return Date().getStrDate(from: startDate)
        }
        else {
            return nil
        }
    }
    
    /// Get End date in string.
    func getEndDate() -> String? {
        if let endDate = self.nStartTime {
            return Date().getStrDate(from: endDate)
        }
        else {
            return nil
        }
    }
}

func getTaskTotalTime(taskId: Int) -> Int {
    let indexTaskId = g_arrCTaskDetails.firstIndex(where: {$0.taskId == taskId})
    if let index = indexTaskId {
        return g_arrCTaskDetails[index].nTotalTime
    }
    else {
        return 0
    }
}

/// Add details.
func updateTaskDetails(pageNo: Int) {
    let arrProj = getAllProjectIds()
    let taskCDCtrlr = TasksCDController()
    g_arrCTaskDetails = taskCDCtrlr.getTaskDetailsFromProjectNameUnFinished(arrProj: arrProj)
    if pageNo > g_taskPageNo {
        g_taskPageNo = pageNo
    }
}

/// Get start time from array of TaskDetails.
func getTaskStartTime(taskId: Int) -> String {
    let indexTaskId = g_arrCTaskDetails.firstIndex(where: {$0.taskId == taskId})
    if let index = indexTaskId {
        if let startTime = g_arrCTaskDetails[index].nStartTime {
            let nDateTime = startTime
            // Convert to date.
            let startDateTime = Date(milliseconds: nDateTime)
            let startTime = startDateTime.timeInDate
            let strTime = getSecondsToHoursMinutesSecondsWithAllFields(seconds: startTime)
            let str12HrTime = convert24to12Format(strTime: strTime)
            
            let strDate = Date().getStrDate(from: nDateTime)
            let strFormateDate = getDateDay(date: strDate)
            
            return "Started \(strFormateDate) \(str12HrTime)"
        }
        else {
            let strFormatedate = getDate()
            let nTime = getTimeInSec()
            let strTime = getSecondsToHoursMinutesSeconds(seconds: nTime)
            let str12HrTime = convert24to12Format(strTime: strTime)
            
            return "Started \(strFormatedate) \(str12HrTime)"
        }
    }
    else {
        return ""
    }
}

/// Get Task name from array of TaskDetails.
func getTaskName(taskId: Int) -> String {
    let indexTaskId = g_arrCTaskDetails.firstIndex(where: {$0.taskId == taskId})
    if let index = indexTaskId {
        return g_arrCTaskDetails[index].taskName
    }
    else {
        return ""
    }
}

func getTotalTaskCountUnFinished(arrProj: Array<Int>) -> Int {
    var count = 0
    for projId in arrProj {
        for cTaskDetails in g_arrCTaskDetails {
            // If project name matches.
            if projId == cTaskDetails.projId {
                count += 1
            }
        }
    }
    return count
}

/// Returns taskdetails object if task id exist in arrCTaskDetails.
func getTaskDetails(taskId: Int) -> TaskDetails? {
    let indexTaskId = g_arrCTaskDetails.firstIndex(where: {$0.taskId == taskId})
    if let index = indexTaskId {
        return g_arrCTaskDetails[index]
    }
    else {
        return nil
    }
}

class TaskTimeDetails {
    var timeId: Int!
    var strDate: String!
    var nStartTime: Int!
    var nEndTime: Int!
    var taskId: Int?
    var description: String!
    
    var nTotalTime : Int {
        return nEndTime - nStartTime
    }
    
    init(timeId: Int, taskId: Int? = nil, date: String, start: Int, end: Int, descr: String?) {
        self.timeId = timeId
        self.taskId = taskId
        self.strDate = date
        self.nStartTime = start
        self.nEndTime = end
        self.description = descr
    }
}

class MonthDetails {
    var arrDates: Array<Int64>
    /// String dates.
    var arrStrDates: Array<String> {
        get {
            var arrStr = Array<String>()
            for date in arrDates {
                let strDate = Date().getStrDate(from: date)
                arrStr.append(strDate)
            }
            return arrStr
        }
    }
    var totalWork: Int {
        get {
            computeTotaltime()
        }
    }
    var strMonthYear: String!
    var arrProj: Array<Int>?
    
    init(nDate: Int64, arrProj: Array<Int>?) {
        self.arrDates = [nDate]
        let strDate = Date().getStrDate(from: nDate)
        self.strMonthYear = getMonthAndYear(strDate: strDate)
        self.arrProj = arrProj
    }
    
    func addDate(nDate: Int64) {
        self.arrDates.append(nDate)
    }
    
    func computeTotaltime() -> Int {
        var totalTime = 0
        let tasksTimeCDCtrlr = TasksTimeCDController()
        for date in arrDates {
            totalTime += tasksTimeCDCtrlr.getTotalWorkTime(intDate: date, arrProj: arrProj ?? [])
        }
        return totalTime
    }
}

class WeekDetails {
    var arrDates: Array<Int64>
    var arrProj: Array<Int>?
    var totalWork: Int {
        get {
            computeTotaltime()
        }
    }
    var weeknumber: Int!
    
    init(nDate: Int64, arrProj: Array<Int>?) {
        self.arrDates = [nDate]
        self.weeknumber = getWeekNumber(nDate: nDate)
        self.arrProj = arrProj
    }
    
    func addDate(nDate: Int64) {
        self.arrDates.append(nDate)
    }
    
    func computeTotaltime() -> Int {
        var totalTime = 0
        let tasksTimeCDCtrlr = TasksTimeCDController()
        for date in arrDates {
            totalTime += tasksTimeCDCtrlr.getTotalWorkTime(intDate: date, arrProj: arrProj ?? [])
        }
        return totalTime
    }
}

/// Email validator.
func isValidEmail(strEmail: String) -> Bool {
    let strRegEx = "[A-Z0-9a-z._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,64}"
    let nsPredEmail = NSPredicate(format: "SELF MATCHES %@", strRegEx)
    let bIsValidEmail = nsPredEmail.evaluate(with: strEmail)
    return bIsValidEmail
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

/// get Start and Date from a week number.
func getStartAndEndDateFromWeekNumber(weekOfYear: Int) -> String
{
    let calendar = Calendar.current
    let year = calendar.component(.yearForWeekOfYear, from: Date())
    let startComponents = DateComponents(weekOfYear: weekOfYear, yearForWeekOfYear: year)
    let start = calendar.date(from: startComponents)!
    let startDate = calendar.date(byAdding: .day, value: 1, to: start)!
    let endComponents = DateComponents(day:7, second: -1)
    let endDate = calendar.date(byAdding: endComponents, to: startDate)!
    let dateFormatter = DateFormatter()
    dateFormatter.dateFormat = "dd"
    let sDay = dateFormatter.string(from: startDate)
    let eDay = dateFormatter.string(from: endDate)
    if startDate.month == endDate.month {
        dateFormatter.dateFormat = "MMMM"
        let month = dateFormatter.string(from: startDate)
        return "\(sDay) - \(eDay) \(month)"
    }
    else {
        let sMonth = startDate.month
        let eMonth = endDate.month
        return "\(sDay) \(sMonth) - \(eDay) \(eMonth)"
    }
}

func getDayWeekMonthInString(strDate: String) -> String {
    let dateFormatter = DateFormatter()
    dateFormatter.dateFormat = "dd/MM/yyyy" //Your date format
    dateFormatter.timeZone = TimeZone(abbreviation: "GMT+0:00") //Current time zone
    //according to date format your date string
    guard let date = dateFormatter.date(from: strDate) else {
        fatalError()
    }
    dateFormatter.dateFormat = "EEEE, MMMM dd"
    //OR dateFormatter.dateFormat = "EEEE, MMMM dd, yyyy"
    let currentDateString: String = dateFormatter.string(from: date)
    print("Current date is \(currentDateString)")
    return currentDateString
}

func getDayNumber(strDate: String) -> Int {
    let formatter  = DateFormatter()
    formatter.dateFormat = "dd/MM/yyyy"
    let todayDate = formatter.date(from: strDate)!
    let myCalendar = NSCalendar(calendarIdentifier: NSCalendar.Identifier.gregorian)!
    let myComponents = myCalendar.components(NSCalendar.Unit.weekday, from: todayDate)
    var weekDay = myComponents.weekday! - 1
    if weekDay == 0 {
        weekDay = 7
    }
    return weekDay
}

func getWeekNumber(nDate: Int64) -> Int {
    let date = Date(milliseconds: nDate)
    let calendar = Calendar.current
    let weekOfYear = calendar.component(.weekOfYear, from: date)
    return weekOfYear
}

/// Converts dd/MM/yyyy string to date object.
func getDateFromString(strDate: String) -> Date {
    let dateFormatter = DateFormatter()
    dateFormatter.dateFormat = "dd/MM/yyyy" //Your date format
    dateFormatter.timeZone = TimeZone(abbreviation: "GMT+0:00") //Current time zone
    //according to date format your date string
    guard let date = dateFormatter.date(from: strDate) else {
        fatalError()
    }
    return date
}

func getDateFromTime(strTime: String) -> Date {
    let dateFormatter = DateFormatter()
    dateFormatter.dateFormat = "dd/MM/yyyy h:mm a" //Your date format
    //according to date format your date string
    guard let date = dateFormatter.date(from: strTime) else {
        fatalError()
    }
    return date
}

func getMonthAndYear(strDate: String) -> String {
    let strDate = strDate
    let words = strDate.split(separator: "/")
    let nMonth = Int(words[1])
    let monthStr = Calendar.current.monthSymbols[nMonth! - 1]
    let start = String.Index(utf16Offset: 0, in: monthStr)
    let end = String.Index(utf16Offset: 3, in: monthStr)
    let strMon = "\(String(monthStr[start..<end])) \(words[2])"
    return strMon
}

/// Function will returns the present date in string type
func getTime() -> String {
    let date = Date()
    let calendar = Calendar.current
    let components = calendar.dateComponents([.hour, .minute, .second], from: date)
    return "\(components.hour!):\(components.minute!):\(components.second!)"
}

/// Function will returns the present date in string type(h:mm a)
func getSecondCount(strTime: String) -> Int {
    let dateF = DateFormatter()
    dateF.dateFormat = "h:mm a"
    let date = dateF.date(from: strTime)
    let calendar = Calendar.current
    let components = calendar.dateComponents([.hour, .minute], from: date!)
    let hr = components.hour
    let min = components.minute
    let totalTime = (hr!*60*60)+(min!*60)
    return totalTime
}

/// Function will returns the present date in string type(HH:mm:ss)
func getSecondCountFormat2(strTime: String) -> Int {
    let dateF = DateFormatter()
    dateF.dateFormat = "HH:mm:ss"
    let date = dateF.date(from: strTime)
    let calendar = Calendar.current
    let components = calendar.dateComponents([.hour, .minute, .second], from: date!)
    let hr = components.hour
    let min = components.minute
    let sec = components.second
    let totalTime = (hr!*60*60)+(min!*60)+(sec!)
    return totalTime
}

/// Returns present time in seconds
func getTimeInSec() -> Int {
    let date = Date()
    let calendar = Calendar.current
    let components = calendar.dateComponents([.hour, .minute, .second], from: date)
    let nTimeSec = components.hour! * 3600 + components.minute! * 60 + components.second!
    return nTimeSec
}

/// Converts seconds to HH:mm:ss format
func getSecondsToHoursMinutesSeconds(seconds : Int, format: TimeFormat
    = .HHmmss) -> String {
    var second = String()
    var minute = String()
    var hour = String()
    if format == .HHmmss {
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
    else {
        var (hr, min, sec) = (seconds / 3600, (seconds % 3600) / 60, (seconds % 3600) % 60)
        if hr == 0 {
            hour = ""
        }
        else {
            hour = "\(hr)h"
        }
        if sec > 30 {
            // If minute greater than 30 sec take ceil value.
            min += 1
        }
        if min == 0 {
            minute = "00m"
        }
        else if min >= 10 {
            minute = "\(min)m"
        }
        else {
            minute = "0\(min)m"
        }
        return "\(hour) \(minute)"
    }
}

/// Converts seconds to hh:mm:ss format
func getSecondsToHoursMinutesSecondsWithAllFields(seconds : Int) -> String {
    var second = String()
    var minute = String()
    var hour = String()
    let (hr, min, sec) = (seconds / 3600, (seconds % 3600) / 60, (seconds % 3600) % 60)
    if hr == 0 {
        hour = "00:"
    }
    else if hr >= 10 {
        hour = "\(hr):"
    }
    else {
        hour = "0\(hr):"
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
    
    /// - returns:
    ///   - if date equals today:  "Today"
    ///   - if date equals to previos day: "Yesterday"
    ///   - else: date in string.
    func getDateDay(date: String) -> String {
        let dateObj = getDateFromString(strDate: date)
    let calendar = Calendar.current
    if calendar.isDateInToday(dateObj) {
        return "Today"
    }
    else if calendar.isDateInYesterday(dateObj) {
        return "Yesterday"
    }
    else {
        return date
    }
}

/// Converts seconds to hh:mm format.
func getSecondsToHourMinute(seconds: Int) -> String {
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

/// Converts 24 hour time to 12 hour time HH:mm:ss format.
func convert24to12Format(strTime: String) -> String {
    let dateF = DateFormatter()
    dateF.dateFormat = "HH:mm:ss"
    let date = dateF.date(from: strTime)
    dateF.dateFormat = "h:mm a"
    return dateF.string(from: date!)
}

/// Converts 12 hour time to 24 hour time HH:mm:ss format.
func convert12to24Format(strTime: String) -> String {
    let dateF = DateFormatter()
    dateF.dateFormat = "h:mm a"
    let date = dateF.date(from: strTime)
    dateF.dateFormat = "HH:mm:ss"
    return dateF.string(from: date!)
}

/// Converts 24 hour time to 12 hour time HH:mm format.
func convert24to12FormatHourMinute(strTime: String) -> String {
    let dateF = DateFormatter()
    dateF.dateFormat = "HH:mm"
    let date = dateF.date(from: strTime)
    dateF.dateFormat = "h:mm a"
    return dateF.string(from: date!)
}


/// Get time from string date and time. (i.e. yyyy:MM:dd HH:mm:ss)
func convertAPILoginTimeToLocal(strDateTime: String) -> String {
    let dateFormatter = DateFormatter()
    dateFormatter.timeZone = TimeZone(abbreviation: "UTC")!
    dateFormatter.dateFormat = "yyyy:MM:dd HH:mm:ss"
    // Convert to UTC Timezone.
    guard let utcDate = dateFormatter.date(from: strDateTime) else {
        fatalError()
    }
    
    // Convert it to Current timezone.
    dateFormatter.timeZone = .current
    let currentDate = dateFormatter.string(from: utcDate)
    return currentDate
}

/// Convert local time to UTC timezone (i.e. dd/MM/yyyy HH:mm:ss to yyyy-MM-dd HH:mm)
func convertLocalTimeToUTC(strDateTime: String) -> String {
    let dateFormatter = DateFormatter()
    dateFormatter.timeZone = .current
    dateFormatter.dateFormat = "dd/MM/yyyy HH:mm:ss"
    // Convert to UTC Timezone.
    guard let utcDate = dateFormatter.date(from: strDateTime) else {
        fatalError()
    }
    
    // Convert it to UTC timezone.
    dateFormatter.timeZone = TimeZone(abbreviation: "UTC")!
    dateFormatter.dateFormat = "yyyy-MM-dd HH:mm"
    let currentDate = dateFormatter.string(from: utcDate)
    return currentDate
}

/// Convert yyyy-MM-dd to dd/MM/yyy
func convertStrDateFormate(strDate: String) -> String {
    let arrDate = strDate.split(separator: "-", maxSplits: 3
        , omittingEmptySubsequences: false)
    let day = String(arrDate[2])
    let mon = String(arrDate[1])
    let year = String(arrDate[0])
    return "\(day)/\(mon)/\(year)"
}

/// Convert  to dd/MM/yyy to yyyy-MM-dd
func convertStrDateFormate2(strDate: String) -> String {
    let arrDate = strDate.split(separator: "/", maxSplits: 3
        , omittingEmptySubsequences: false)
    let year = String(arrDate[2])
    let mon = String(arrDate[1])
    let day = String(arrDate[0])
    return "\(year)-\(mon)-\(day)"
}

/// Convert local time to UTC timezone (i.e. dd/MM/yyyy HH:mm:ss to yyyy-MM-dd HH:mm)
func convertLocalTimeToUTC(strTime: String) -> String {
    let dateFormatter = DateFormatter()
    dateFormatter.timeZone = .current
    dateFormatter.dateFormat = "h:mm a"
    // Convert to UTC Timezone.
    guard let utcDate = dateFormatter.date(from: strTime) else {
        fatalError()
    }
    
    // Convert it to UTC timezone.
    dateFormatter.timeZone = TimeZone(abbreviation: "UTC")!
    dateFormatter.dateFormat = "HH:mm"
    let currentDate = dateFormatter.string(from: utcDate)
    return currentDate
}

/// Convert local time to UTC timezone (i.e. dd/MM/yyyy h:mm a to yyyy-MM-dd)
func convertLocalDateToUTC(strDate: String, format: String) -> String {
    let dateFormatter = DateFormatter()
    dateFormatter.timeZone = .current
    dateFormatter.dateFormat = format
    // Convert to UTC Timezone.
    guard let utcDate = dateFormatter.date(from: strDate) else {
        fatalError()
    }
    
    // Convert it to UTC timezone.
    dateFormatter.timeZone = TimeZone(abbreviation: "UTC")!
    dateFormatter.dateFormat = "yyyy-MM-dd"
    let currentDate = dateFormatter.string(from: utcDate)
    return currentDate
}

/// Get time from string date and time.. (i.e. yyyy-MM-dd HH:mm:ss)
func convertAPITimeToLocal(strDateTime: String) -> String {
    let dateFormatter = DateFormatter()
    dateFormatter.dateFormat = "yyyy-MM-dd HH:mm:ss"
    dateFormatter.timeZone = TimeZone(abbreviation: "UTC")!
    // Convert to UTC Timezone.
    guard let utcDate = dateFormatter.date(from: strDateTime) else {
        // If not valid.
        return "invalid"
    }
    
    // Convert it to Current timezone.
    dateFormatter.timeZone = .current
    let currentDate = dateFormatter.string(from: utcDate)
    return currentDate
}

/// Get time from string date and time.. (i.e. yyyy-MM-dd HH:mm:ss)
func convertStrDateTimeToDate(strDateTime: String, format: String = "dd/MM/yyyy HH:mm:ss") -> Date {
    let dateFormatter = DateFormatter()
    dateFormatter.timeZone = .current
    dateFormatter.dateFormat = format
    // Convert to UTC Timezone.
    guard let date = dateFormatter.date(from: strDateTime) else {
        fatalError()
    }
    return date
}

/// Get time from string date and time.. (i.e. yyyy-MM-dd HH:mm:ss)
func convertUTCtoLocal(strDateTime: String) -> Date {
    let dateFormatter = DateFormatter()
    dateFormatter.timeZone = TimeZone(abbreviation: "UTC")
    dateFormatter.dateFormat = "yyyy-MM-dd HH:mm:ss"
    guard let date = dateFormatter.date(from: strDateTime) else {
        fatalError()
    }
    return date
}

/// get string date (dd/MM/yyyy).
func getStrDateTime(date: Date) -> String {
    let dateFormatter = DateFormatter()
    dateFormatter.dateFormat = "dd/MM/yyyy"
    return dateFormatter.string(from: date)
}

/// String date in (HH:mm:sss)
func getStrTime(date: Date) -> String {
    let dateFormatter = DateFormatter()
    dateFormatter.dateFormat = "HH:mm:ss"
    return dateFormatter.string(from: date)
}

/// String date in (dd/MM/yyyy HH:mm:sss)
func getCurrentDateTime() -> String {
    let dateFormatter = DateFormatter()
    dateFormatter.dateFormat = "dd/MM/yyyy HH:mm:ss"
    return dateFormatter.string(from: Date())
}

/// String date in (HH:mm:sss)
func getCurrentTime() -> String {
    let dateFormatter = DateFormatter()
    dateFormatter.dateFormat = "HH:mm:ss"
    return dateFormatter.string(from: Date())
}

/// String date in (dd/MM/yyyy)
func getCurrentDate() -> String {
    let dateFormatter = DateFormatter()
    dateFormatter.dateFormat = "dd/MM/yyyy"
    return dateFormatter.string(from: Date())
}

/// Downloads image from url and appends it to the image view.
func downloadImage(from url: URL, cProjectDetails: ProjectDetails) {
    getData(from: url) { data, response, error in
        guard let data = data, error == nil else { return }
        cProjectDetails.imgProjIcon = UIImage(data: data)
    }
}

/// Downloads image from url and appends it to the image view.
func downloadImage(from url: URL, imgView: UIImageView) {
    getData(from: url) { data, response, error in
        guard let data = data, error == nil else { return }
        DispatchQueue.main.async() {
            imgView.image = UIImage(data: data)
        }
    }
}

/// Add image to the image variable.
func downloadImage(from url: URL, completion: @escaping (Data) -> ()) {
    getData(from: url) { data, response, error in
        guard let data = data, error == nil else { return }
        completion(data)
    }
}

/// recieves data from api.
///
/// - parameters:
///   - url: API url.
///   - completon: Handle recieved data.
func getData(from url: URL, completion: @escaping (Data?, URLResponse?, Error?) -> ()) {
    URLSession.shared.dataTask(with: url, completionHandler: completion).resume()
}

/// Touch area with minof 44 to top and bottom..
class ButtonDayGraph: UIButton
{
    override func point( inside point: CGPoint, with event: UIEvent? ) -> Bool
    {
        let relativeFrame = self.bounds
        let hitTestEdgeInsets = UIEdgeInsets( top: -7, left: 0, bottom: -7, right: 0 )
        let hitFrame = relativeFrame.inset( by: hitTestEdgeInsets )
        self.contentEdgeInsets = UIEdgeInsets(top: -2, left: 0, bottom: -2, right: 0 )
        return hitFrame.contains( point )
    }
}

/// Touch area with min of 44 to left and right..
class ButtonWeekGraph: UIButton
{
    var colors : [UIColor?] = [UIColor?]() {
        didSet {
            self.setNeedsDisplay()
        }
    }
    
    var values : [CGFloat] = [CGFloat]() {
        didSet {
            self.setNeedsDisplay()
        }
    }
    
    override init(frame: CGRect) {
        super.init(frame: frame)
        backgroundColor = .clear
    }
    
    required init?(coder aDecoder: NSCoder) {
        super.init(coder: aDecoder)
    }
    
    override func draw(_ rect: CGRect) {
        let r = self.bounds
        // number of segments to render
        let numberOfSegments = values.count
        // get the current context
        let ctx = UIGraphicsGetCurrentContext()
        // store a cumulative value in order to start each line after the last one
        var cumulativeValue:CGFloat = 0
        for i in 0..<numberOfSegments {
            // set fill color to the given color
            ctx!.setFillColor(colors[i]!.cgColor)
            // fill that given segment
            ctx!.fill(CGRect(x: 0, y: cumulativeValue*r.size.height, width: r.size.width
                , height: values[i]*r.size.height))
            cumulativeValue += values[i]
        }
    }
    
    override func point(inside point: CGPoint, with event: UIEvent?) -> Bool
    {
        let relativeFrame = self.bounds
        let hitTestEdgeInsets = UIEdgeInsets( top: -7, left: -7, bottom: -7, right: -7 )
        let hitFrame = relativeFrame.inset(by: hitTestEdgeInsets)
        return hitFrame.contains(point)
    }
}

@IBDesignable

class MonthDayLabel: UILabel {
    
    var colors : [UIColor?] = [UIColor?]() {
        didSet {
            self.setNeedsDisplay()
        }
    }
    
    var values : [CGFloat] = [CGFloat]() {
        didSet {
            self.setNeedsDisplay()
        }
    }
    
    override init(frame: CGRect) {
        super.init(frame: frame)
        backgroundColor = .clear
    }
    
    required init?(coder aDecoder: NSCoder) {
        super.init(coder: aDecoder)
    }
    
    override func draw(_ rect: CGRect) {
        super.draw(rect)
        let width = self.bounds.width
        let numberOfSegments = values.count
        let ctx = UIGraphicsGetCurrentContext()
        // store a cumulative value in order to start each line after the last one
        var startAngle = -CGFloat.pi * 0.5
        let viewCenter = width/CGFloat(2)
        for i in 0..<numberOfSegments {
            ctx!.setFillColor(colors[i]!.cgColor)
            let endAngle = startAngle + 2 * .pi * (values[i])
            ctx?.move(to: CGPoint(x: viewCenter, y: viewCenter))
            ctx?.addArc(center: CGPoint(x: viewCenter, y: viewCenter), radius: viewCenter
                , startAngle: startAngle, endAngle: endAngle, clockwise: false)
            ctx!.fillPath()
            startAngle = endAngle
        }
    }
}

@IBDesignable

class StateButton: UIButton {
    var isHighlightedCustom = false
    
    override func awakeFromNib() {
        super.awakeFromNib()
        self.layer.cornerRadius = self.frame.width / 2
        self.layer.shadowColor = g_colorMode.invertColor().withAlphaComponent(0.4).cgColor
        self.layer.shadowOffset = CGSize(width: 0, height: 3)
        self.layer.shadowRadius = 5
        self.layer.shadowOpacity = 0.5
        self.layer.masksToBounds = false
    }
    
    override var isHighlighted: Bool {
        didSet {
            // Custom highlight on touch.
            if isHighlighted && !isHighlightedCustom {
                let shadowLayer = CAShapeLayer()
                shadowLayer.path = UIBezierPath(roundedRect: bounds
                    , cornerRadius: self.bounds.height / 2).cgPath
                shadowLayer.fillColor = UIColor.white.withAlphaComponent(0.1).cgColor
                shadowLayer.shadowPath = shadowLayer.path
                    layer.addSublayer(shadowLayer)
                    isHighlightedCustom = true
                
            }
            else if isHighlightedCustom && !isHighlighted {
                UIView.animate(withDuration: 0.5) {
                    self.layer.sublayers?.removeLast()
                }
                isHighlightedCustom = false
            }
        }
    }
}
