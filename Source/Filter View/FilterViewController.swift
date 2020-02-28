/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : FilterViewController.swift
 //
 //    File Created      : 24:Sept:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : Filter view controller.
 //
 //////////////////////////////////////////////////////////////////////////// */

protocol FilterDelegate {
    ///Selected projects from filter view.
    func selectedProjects(arrProj: Array<Int>)
    ///To handle filter view dismiss.
    func dismissedView()
    ///Handle clear filter pressed in filer view.
    func clearedFilter()
    ///Selected sorting method in filer view.
    func sortApplied(sortType: SortTypes, indexSelected: IndexPath)
    /// Send opacity to background view.
    func changedFilterViewPosition(cgFAlpha: CGFloat)
}

import UIKit

class FilterViewController: UIViewController, UITableViewDelegate, UITableViewDataSource {
    @IBOutlet weak var btnProject: UIButton!
    @IBOutlet weak var btnDone: UIButton!
    @IBOutlet weak var tbleProject: UITableView!
    @IBOutlet weak var btnClearFilter: UIButton!
    @IBOutlet weak var lblResultsCounter: UILabel!
    @IBOutlet weak var lblProjectIndicator: UILabel!
    @IBOutlet weak var lblSortIndicator: UILabel!
    @IBOutlet weak var viewMain: UIView!
    @IBOutlet weak var viewFooter: UIView!
    @IBOutlet weak var btnSort: UIButton!
    @IBOutlet weak var viewFilterSelection: UIView!
    @IBOutlet weak var viewHeader: UIView!
    @IBOutlet weak var viewButtons: UIView!
    @IBOutlet weak var nsLViewMainHeight: NSLayoutConstraint!
    @IBOutlet weak var lblNoProject: UILabel!
    
    var taskCDController: TasksCDController!
    var delegate: FilterDelegate?
    var arrSelectedProj: Array<Int>!
    var nResults: Int!
    var bIsProjectSelection = true
    var arrSortTypes: Array<SortTypes>!
    var indexSelected: IndexPath!
    let arrSortValues = ["Tasks", "Projects", "Duration"]
    var dictProjectUrl: Dictionary<String, UIImage>!
    var arrImage = [#imageLiteral(resourceName: "ActivityIcon"), #imageLiteral(resourceName: "ProjectIcon"), #imageLiteral(resourceName: "DurationIcon")]
    var cgFMidYViewMain: CGFloat!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        // Initialise CD controller.
        taskCDController = TasksCDController()
        
        tbleProject.delegate = self
        tbleProject.dataSource = self
        tbleProject.separatorInset = .zero
        tbleProject.separatorStyle = .none
        
        viewMain.backgroundColor = g_colorMode.defaultColor()
        viewMain.layer.masksToBounds = true
        viewMain.layer.cornerRadius = 35
        viewMain.layer.borderColor = g_colorMode.lineColor().cgColor
        viewMain.layer.borderWidth = 0.3
        viewButtons.clipsToBounds = false
        
        let panGesture = UIPanGestureRecognizer(target: self, action:#selector(self
            .handlePanGesture(panGesture:)))
        viewMain.addGestureRecognizer(panGesture)
        cgFMidYViewMain = 560
        nsLViewMainHeight.constant = cgFMidYViewMain
        
        viewHeader.backgroundColor = g_colorMode.defaultColor()
        viewFooter.backgroundColor = g_colorMode.defaultColor()
        tbleProject.backgroundColor = g_colorMode.defaultColor()
        
        lblSortIndicator.layer.masksToBounds = true
        lblProjectIndicator.layer.masksToBounds = true
        btnClearFilter.isHidden = true
        
        // Update results count label.
        if let arrProj = arrSelectedProj {
            nResults = getTotalTaskCountUnFinished(arrProj: arrProj)
            lblProjectIndicator.backgroundColor = UIColor(cgColor: g_colorMode.startColor())
        }
        else {
            let arrProjIds = getAllProjectIds()
            nResults = getTotalTaskCountUnFinished(arrProj: arrProjIds)
        }
        
        if let _ = indexSelected {
            // Indicator for applied filter.
            lblSortIndicator.backgroundColor = UIColor(cgColor: g_colorMode.startColor())
        }
        
        if nil != arrSelectedProj || nil != indexSelected {
            btnClearFilter.isHidden = false
        }
        
        self.lblResultsCounter.text = "\(nResults!) results found"
        let cgPStart = CGPoint(x: 0, y: 0.25)
        let cgPEnd = CGPoint(x: 1, y: 0.75)
        
        btnDone.addGradient(cgPStart: cgPStart, cgPEnd: cgPEnd, cgFRadius: 15)
        
        btnProject.setTitleColor(g_colorMode.textColor(), for: .normal)
        btnClearFilter.setTitleColor(g_colorMode.textColor(), for: .normal)
        btnSort.setTitleColor(g_colorMode.textColor(), for: .normal)
        btnProject.backgroundColor = g_colorMode.defaultColor()
        arrSortTypes = [SortTypes.tasks, SortTypes.projects,
                        SortTypes.duration]
        
        btnProject.titleLabel?.font = UIFont.boldSystemFont(ofSize: 15.0)
        btnSort.titleLabel?.font = UIFont.systemFont(ofSize: 15.0)
        
        // Check filter applied from activty VC.
        if delegate is MyActivityViewController {
            self.btnSort.isHidden = true
            self.lblResultsCounter.isHidden = true
        }
    }
    
    override func viewDidAppear(_ animated: Bool) {
        super.viewDidAppear(false)
        
        // Apply drop shadow.
        viewHeader.drawShadowFromBzrPath()
        viewFooter.inverseShadowToTop()
        viewButtons.addInsideShadow(to: [.right], radius: 1, opacity: 0.2
            , color: g_colorMode.lineColor().cgColor)
    }
    
    // Handle pangetsture to main view. (Filter view)
    @objc func handlePanGesture(panGesture: UIPanGestureRecognizer) {
        let translation = panGesture.translation(in: self.view)
        if cgFMidYViewMain + translation.y >= cgFMidYViewMain {
            // Transform with pan translation.
            nsLViewMainHeight.constant = cgFMidYViewMain - translation.y
            let progress = (cgFMidYViewMain - nsLViewMainHeight.constant) / (cgFMidYViewMain)
            delegate?.changedFilterViewPosition(cgFAlpha: progress)
        }
        if panGesture.state == .ended || panGesture.state == .cancelled ||
            panGesture.state == .failed {
            // If table moved below to more than half of its height.
            if nsLViewMainHeight.constant < cgFMidYViewMain/2 || panGesture.velocity(in: view).y > 500 {
                // Dismiss view.
                nsLViewMainHeight.constant = 0
                UIView.animate(withDuration: 0.2, animations: {
                    self.view.layoutIfNeeded()
                    self.delegate?.changedFilterViewPosition(cgFAlpha: 1)
                }) { _ in
                    self.delegate?.dismissedView()
                    self.dismiss(animated: true, completion: nil)
                }
            }
            else {
                // Move to original height.
                nsLViewMainHeight.constant = cgFMidYViewMain
                UIView.animate(withDuration: 0.5, animations: {
                    self.view.layoutIfNeeded()
                    self.delegate?.changedFilterViewPosition(cgFAlpha: 0.2)
                })
            }
        }
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        if bIsProjectSelection {
            // If no projects assigned.
            if g_dictProjectDetails.count == 0 {
                lblNoProject.isHidden = false
            }
            else {
                lblNoProject.isHidden = true
            }
            return g_dictProjectDetails.count
        }
        else {
            lblNoProject.isHidden = true
            return 3
        }
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "ProjectCell") as!
            FilterProjectCell
        // If filter from projects.
        if bIsProjectSelection {
            let projId: Int = Array(g_dictProjectDetails.keys)[indexPath.row]
            let projName = g_dictProjectDetails[projId]?.projName!
            cell.lblProjectName.text = projName
            cell.imgProjectLogo.image = nil
            cell.imgProjectLogo?.image = g_dictProjectDetails[projId]?.imgProjIcon
            cell.imgSelect.backgroundColor = g_colorMode.midColor()
            cell.imgSelect.isHidden = true
            if let arrProjs = arrSelectedProj {
                if arrProjs.contains(projId) {
                    cell.imgSelect.isHidden = false
                }
            }
        }
        // If filter based on sort type.
        else {
            cell.lblProjectName.text = arrSortValues[indexPath.row]
            cell.imgSelect.backgroundColor = .clear
            cell.imgProjectLogo.image = arrImage[indexPath.row]
            cell.imgSelect.isHidden = true
            if let index = indexSelected {
                if index == indexPath {
                    cell.imgSelect.backgroundColor = g_colorMode.midColor()
                    cell.imgSelect.isHidden = false
                }
            }
        }
        return cell
    }
    
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
        return 44
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        /// If selection type is filter.
        if bIsProjectSelection {
            let projId: Int = Array(g_dictProjectDetails.keys)[indexPath.row]
            if let _ = arrSelectedProj {
                if arrSelectedProj.contains(projId) {
                    let index = arrSelectedProj.firstIndex(of: (projId))
                    arrSelectedProj.remove(at: index!)
                }
                else {
                    arrSelectedProj.append(projId)
                }
            }
            else {
                arrSelectedProj = Array<Int>()
                arrSelectedProj.append(projId)
            }
            
            // Disable Done btn when no project selected.
            if arrSelectedProj.count == 0 {
                btnDone.isEnabled = false
                UIView.animate(withDuration: 0.5) {
                    self.btnDone.alpha = 0.2
                }
            }
            else {
                btnDone.isEnabled = true
                UIView.animate(withDuration: 0.5) {
                    self.btnDone.alpha = 1
                }
            }
            
            updateCountLabel()
            tbleProject.reloadRows(at: [indexPath], with: .none)
        }
        // If selection type is sort.
        else {
            indexSelected = indexPath
            tbleProject.reloadData()
        }
    }
    
    /// Updates result count in label.
    func updateCountLabel() {
        nResults = taskCDController.getTotalTaskCountUnFinished(arrProj: arrSelectedProj)
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
        btnProject.titleLabel?.font = UIFont.boldSystemFont(ofSize: 15.0)
        btnSort.titleLabel?.font = UIFont.systemFont(ofSize: 15.0)
        lblResultsCounter.isHidden = false
        bIsProjectSelection = true
        tbleProject.reloadData()
    }
    
    @IBAction func btnSortPressed(_ sender: Any) {
        btnSort.titleLabel?.font = UIFont.boldSystemFont(ofSize: 15.0)
        btnProject.titleLabel?.font = UIFont.systemFont(ofSize: 15.0)
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
            delegate?.sortApplied(sortType: arrSortTypes[index.row], indexSelected: index)
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
    
    override func traitCollectionDidChange(_ previousTraitCollection: UITraitCollection?) {
        super.traitCollectionDidChange(previousTraitCollection)
        
        guard UIApplication.shared.applicationState == .inactive else {
            return
        }
        
        if #available(iOS 12.0, *) {
            if self.traitCollection.userInterfaceStyle == .light {
                UserDefaults.standard.setValue(1, forKey: "colorMode")
            }
            else {
                UserDefaults.standard.setValue(2, forKey: "colorMode")
            }
            setColorMode()
        }
    }
}
