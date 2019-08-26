//
//  ActivityDetailsView.swift
//  Attendance Manager
//
//  Created by Sachin on 1/3/20.
//  Copyright © 2020 Sachin. All rights reserved.
//

import UIKit

class ActivityDetailsView: UIViewController, UITableViewDelegate, UITableViewDataSource {
    
    @IBOutlet weak var btnBack: UIButton!
    @IBOutlet weak var viewHeader: UIView!
    @IBOutlet weak var tblTaskDetails: UITableView!
    @IBOutlet weak var lblHeader: UILabel!
    
    var arrDictTaskDetails: Array<Dictionary<String, Any>>!
    var taskUpdater: TaskUpdater!
    var projectUpdater: AddProjects!
    var arrStrDate: Array<String>!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        viewHeader.addGradient(startColor: cgCForGradientStart, endColor: cgCForGradientEnd)
        tblTaskDetails.delegate = self
        tblTaskDetails.dataSource = self
        taskUpdater = TaskUpdater()
        projectUpdater = AddProjects()
        tblTaskDetails.register(UINib(nibName: "userBreakInfoCell", bundle: nil),
        forCellReuseIdentifier: "userBreakInfoCell")
        arrDictTaskDetails = taskUpdater.getDataFromDate(arrDate: arrStrDate)
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        arrDictTaskDetails.count
    }
    
    func numberOfSections(in tableView: UITableView) -> Int {
        return 1
    }
    
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
        let dictValue = arrDictTaskDetails[indexPath.row]
        let taskName = dictValue["Task Name"] as! String
               
        let label = UILabel(frame: CGRect(x: 0, y: 0, width: UIScreen.main.bounds.width - 57, height: 20))
        label.numberOfLines = 0
        label.lineBreakMode = .byWordWrapping
        label.text = taskName
        label.sizeToFit()
        return 94 + label.bounds.height
    }
    
    func tableView(_ tableView: UITableView, estimatedHeightForHeaderInSection section: Int) -> CGFloat {
        return 50
    }
    
    func tableView(_ tableView: UITableView, willDisplayHeaderView view: UIView, forSection section: Int) {
        if let headerView = view as? UITableViewHeaderFooterView {
            headerView.textLabel?.textAlignment = .center
            headerView.contentView.backgroundColor = .white
            headerView.textLabel?.textColor = .lightGray
            headerView.textLabel?.font = headerView.textLabel?.font.withSize(12)
            headerView.layoutIfNeeded()
        }
    }
    
    func tableView(_ tableView: UITableView, titleForHeaderInSection section: Int) -> String? {
        if arrDictTaskDetails.count > 1 {
            return "Activities"
        }
        else {
            return "Activity"
        }
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "userBreakInfoCell",
                        for: indexPath) as! UserTaskInfoCell
        //        let dictValues = taskUpdater.getAllData(taskId: indexPath.row + 1)
                print("Mes \(indexPath)")
                let dictValues = arrDictTaskDetails[indexPath.row]
                
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
                downloadImage(from: projectUpdater.getProjectIconUrl(projectId: projId),
                              imgView: cell.imgVProjectIcon)
                cell.selectionStyle = .none
                return cell
    }

    deinit {
        print("Details View Deinitialised")
    }
    
    @IBAction func btnBackPressed(_ sender: Any) {
        self.presentingViewController?.dismiss(animated: true, completion: {self.view = nil})
    }
    
}
