//
//  FilterView.swift
//  Attendance Manager
//
//  Created by Sachin on 9/24/19.
//  Copyright © 2019 Sachin. All rights reserved.
//

protocol FilterDelegate {
    func selectedProjects(arrProj: Array<String>)
    func dismissedView()
    func clearedFilter()
    func sortApplied(strSortType: String, indexSelected: IndexPath)
}

import UIKit

class FilterViewController: UIViewController, UITableViewDelegate, UITableViewDataSource {
   
    @IBOutlet weak var btnProject: UIButton!
//    @IBOutlet weak var btnCancel: UIButton!
    @IBOutlet weak var btnDone: UIButton!
    @IBOutlet weak var tbleProject: UITableView!
    @IBOutlet weak var btnClearFilter: UIButton!
    @IBOutlet weak var lblResultsCounter: UILabel!
    @IBOutlet weak var lblProjectIndicator: UILabel!
    @IBOutlet weak var lblSortIndicator: UILabel!
    @IBOutlet weak var viewMain: UIView!
    @IBOutlet weak var nsLViewWidth: NSLayoutConstraint!
    @IBOutlet weak var btnSort: UIButton!
    
    var projectUpdater: AddProjects!
    var taskUpdater: TaskUpdater!
    var arrProjName: Array<String>!
    var delegate: FilterDelegate?
    var arrSelectedProj: Array<String>!
    var nResults: Int!
    var bIsProjectSelection = true
    var arrSortTypes: Array<String>!
    var indexSelected: IndexPath!
    let arrSortValues = ["Tasks", "Projects", "Duration"]
    var dictProjectUrl: Dictionary<String, URL>!
    var arrImage = [#imageLiteral(resourceName: "ActivityIcon"), #imageLiteral(resourceName: "ProjectIcon"), #imageLiteral(resourceName: "DurationIcon")]
    
    override func viewDidLoad() {
        super.viewDidLoad()
        projectUpdater = AddProjects()
        taskUpdater = TaskUpdater()
        arrProjName = Array<String>()
        arrProjName = projectUpdater.getAllProjectNames()
        dictProjectUrl = projectUpdater.getProjectNameAndIconUrl()
        tbleProject.delegate = self
        tbleProject.dataSource = self
        tbleProject.separatorInset = .zero
        tbleProject.separatorStyle = .none
        viewMain.layer.masksToBounds = true
        lblSortIndicator.layer.masksToBounds = true
        lblProjectIndicator.layer.masksToBounds = true
        
        if let arrProj = arrSelectedProj {
            nResults = taskUpdater.getTotalTaskCount(arrProj: arrProj)
            lblProjectIndicator.backgroundColor = UIColor(cgColor: cgCForGradientStart)
        }
        else {
            nResults = taskUpdater.getTotalTaskCount(arrProj: arrProjName)
        }
        if let _ = indexSelected {
            lblSortIndicator.backgroundColor = UIColor(cgColor: cgCForGradientStart)
        }
        self.lblResultsCounter.text = "\(nResults!) results found"
        btnDone.addGradient(startColor: cgCForGradientStart, endColor: cgCForGradientEnd,
                            cgFRadius: 5)
//        btnCancel.setTitleColor(UIColor(cgColor: cgCForGradientStart), for: .normal)
        btnProject.setTitleColor(UIColor(cgColor: cgCForGradientStart), for: .normal)
        btnClearFilter.setTitleColor(UIColor(cgColor: cgCForGradientStart), for: .normal)
        btnSort.setTitleColor(UIColor(cgColor: cgCForGradientStart), for: .normal)
        arrSortTypes = [SortTypes.tasks.rawValue, SortTypes.projects.rawValue, SortTypes.duration.rawValue]
//        tbleProject.register(FilterProjectCell.self, forCellReuseIdentifier: "ProjectCell")
    }
    
    override func viewDidAppear(_ animated: Bool) {
        super.viewDidAppear(true)
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        if bIsProjectSelection {
            return arrProjName.count
        }
        else {
            return 3
        }
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "ProjectCell") as!
            FilterProjectCell
        if bIsProjectSelection {
            cell.lblProjectName.text = arrProjName[indexPath.row]
            cell.imgProjectLogo.image = nil
            downloadImage(from: dictProjectUrl[arrProjName[indexPath.row]]!, imgView:
                cell.imgProjectLogo)
            cell.imgSelect.backgroundColor = .clear
            if let arrProjs = arrSelectedProj {
                if arrProjs.contains(cell.lblProjectName.text!) {
                    cell.imgSelect.backgroundColor = UIColor(cgColor: cgCForGradientStart)
                }
            }
        }
        else {
            cell.lblProjectName.text = arrSortValues[indexPath.row]
            cell.imgSelect.backgroundColor = .clear
            cell.imgProjectLogo.image = arrImage[indexPath.row]
            if let index = indexSelected {
                if index == indexPath {
                    cell.imgSelect.backgroundColor = UIColor(cgColor: cgCForGradientStart)
                }
            }
        }
        return cell
    }
    
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
        return 25
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        let cell = tableView.cellForRow(at: indexPath) as! FilterProjectCell
        if bIsProjectSelection {
            if let _ = arrSelectedProj {
                if arrSelectedProj.contains(cell.lblProjectName.text!) {
                    let index = arrSelectedProj.firstIndex(of: (cell.lblProjectName.text!))
                    arrSelectedProj.remove(at: index!)
                }
                else {
                    arrSelectedProj.append(cell.lblProjectName.text!)
                }
            }
            else {
                arrSelectedProj = Array<String>()
                arrSelectedProj.append(cell.lblProjectName.text!)
            }
            updateCountLabel()
            tbleProject.reloadRows(at: [indexPath], with: .none)
        }
        else {
            indexSelected = indexPath
            tbleProject.reloadData()
        }
    }
    
    func updateCountLabel() {
        nResults = taskUpdater.getTotalTaskCount(arrProj: arrSelectedProj)
        self.lblResultsCounter.text = "\(nResults!) results found"
    }
    
    
    @IBAction func btnClearFilterPressed(_ sender: Any) {
        delegate?.clearedFilter()
        self.dismiss(animated: true, completion: nil)
    }
    
    deinit {
        print("FilterView Deinitialised")
    }

    @IBAction func btnProjectPressed(_ sender: Any) {
        btnProject.backgroundColor = .white
        btnSort.backgroundColor = .clear
        lblResultsCounter.isHidden = false
        bIsProjectSelection = true
        tbleProject.reloadData()
    }
    
    @IBAction func btnSortPressed(_ sender: Any) {
        btnProject.backgroundColor = .clear
        btnSort.backgroundColor = .white
        lblResultsCounter.isHidden = true
        bIsProjectSelection = false
        tbleProject.reloadData()
    }
    
    @IBAction func btnCancelPressed(_ sender: Any) {
        delegate?.dismissedView()
        self.dismiss(animated: true, completion: nil)
    }
    
    @IBAction func btnDonePressed(_ sender: Any) {
        if let index = indexSelected {
            delegate?.sortApplied(strSortType: arrSortTypes[index.row], indexSelected: index)
        }
        if let arrProj = arrSelectedProj {
            delegate?.selectedProjects(arrProj: arrProj)
        }
        if arrSelectedProj == nil && indexSelected == nil {
            delegate?.dismissedView()
        }
        self.dismiss(animated: true, completion: nil)
    }
    
    @IBAction func viewPressed(_ sender: Any) {
        delegate?.dismissedView()
        self.dismiss(animated: true, completion: nil)
    }
}
