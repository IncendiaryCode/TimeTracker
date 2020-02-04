/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : AccountViewController.swift
 //
 //    File Created      : 06:Nov:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : User account information.
 //
 //////////////////////////////////////////////////////////////////////////// */

import UIKit

class AccountViewController: UIViewController, UITableViewDataSource, UITableViewDelegate,
UIGestureRecognizerDelegate, UINavigationControllerDelegate, UIImagePickerControllerDelegate {
    
    @IBOutlet weak var imgVProfile: UIImageView!
    @IBOutlet weak var txtUsername: UITextField!
    @IBOutlet weak var btnChangePswd: UIButton!
    @IBOutlet weak var btnLogout: UIButton!
    @IBOutlet weak var viewButtons: UIView!
    @IBOutlet weak var btnDisplayMode: UIButton!
    @IBOutlet weak var tblDisplayModes: UITableView!
    @IBOutlet weak var nsLDisModeHeight: NSLayoutConstraint!
    @IBOutlet weak var actIndicator: UIActivityIndicatorView!
    @IBOutlet weak var nsLBtnDisModeHeight: NSLayoutConstraint!
    @IBOutlet weak var btnMultiTask: UIButton!
    @IBOutlet weak var actIndicatorProfile: UIActivityIndicatorView!
    @IBOutlet weak var scrollView: UIScrollView!
    
    @IBOutlet weak var btnAbout: UIButton!
    @IBOutlet weak var btnResetIntro: UIButton!
    @IBOutlet weak var lblAccSetting: UILabel!
    @IBOutlet weak var lblAppSetting: UILabel!
    @IBOutlet weak var viewAccount: UIView!
    @IBOutlet weak var viewApp: UIView!
    @IBOutlet weak var viewBG: UIView!
    @IBOutlet weak var txtEmailPhone: UITextField!
    @IBOutlet weak var nsLtxtFCenter: NSLayoutConstraint!
    @IBOutlet weak var btnEditSave: UIButton!
    @IBOutlet weak var nsLTxtFEmailTop: NSLayoutConstraint!
    @IBOutlet weak var txtPhone: UITextField!
    @IBOutlet weak var nsLScrollViewTop: NSLayoutConstraint!
    @IBOutlet weak var nsLTxtUsernameTop: NSLayoutConstraint!
    @IBOutlet weak var btnChangeImage: UIButton!
    @IBOutlet weak var btnCancel: UIButton!
    
    
    var cgFTableDisHeight: CGFloat!
    var punchInOutCDController: PunchInOutCDController!
    var projectCDController: ProjectsCDController!
    var tasksCDController: TasksCDController!
    var taskTimeCDController: TasksTimeCDController!
    
    enum TableSelection {
        case displayMode
        case multiTask
    }
    var tableSelection: TableSelection!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        updateViewsAndColor()

        // Draw shadows.
        btnChangePswd.drawShadow()
        btnLogout.drawShadow()
        btnDisplayMode.drawShadow()
        btnAbout.drawShadow()
        btnResetIntro.drawShadow()
        btnMultiTask.drawShadow()
        
        // Tap gesture to button view.
        var tap = UITapGestureRecognizer(target: self, action: #selector
            (viewBtnsPressed(_:)))
        tap.delegate = self
        tap.numberOfTapsRequired = 1
        viewButtons.addGestureRecognizer(tap)
        
        viewBG.backgroundColor = UIColor.black.withAlphaComponent(0.7)
        txtEmailPhone.textColor = g_colorMode.defaultColor().withAlphaComponent(0.5)
        
        // Tap gesture to BG view.
        tap = UITapGestureRecognizer(target: self, action: #selector
            (viewBGPressed(_:)))
        tap.delegate = self
        tap.numberOfTapsRequired = 1
        viewBG.addGestureRecognizer(tap)
        
        tap = UITapGestureRecognizer(target: self, action: #selector
            (imgProfileTapped(sender:)))
        imgVProfile.addGestureRecognizer(tap)
        
        let panGesture = UIPanGestureRecognizer(target: self, action:#selector(self
            .panToTableview(panGesture:)))
        tblDisplayModes.addGestureRecognizer(panGesture)
        
        imgVProfile.image = #imageLiteral(resourceName: "personIcon")
        if let strName = UserDefaults.standard.string(forKey: "username") {
            txtUsername.text = strName
        }
        else {
            txtUsername.text = ""
        }
        
        setUpEmailPhone()
        
        // Update image
        if let img = g_userProfile {
            imgVProfile.image = img
        }
        else {
            if let strUrl = UserDefaults.standard.value(forKey: "profileUrl") as? String {
                if let url = URL(string: strUrl) {
                    actIndicatorProfile.startAnimating()
                    imgVProfile.alpha = 0.5
                    downloadImage(from: url) {
                        data in
                        DispatchQueue.main.async {
                            let img = UIImage(data: data)
                            self.imgVProfile.image = img
                            g_userProfile = img
                            self.imgVProfile.alpha = 1
                            self.actIndicatorProfile.stopAnimating()
                            self.imgVProfile.setNeedsDisplay()
                        }
                    }
                }
            }
        }
        
        tblDisplayModes.dataSource = self
        tblDisplayModes.delegate = self
        tblDisplayModes.layer.borderColor = g_colorMode.lineColor().cgColor
        cgFTableDisHeight = UIScreen.main.bounds.midY
        
        // Initialise Core data controller objects.
        punchInOutCDController = PunchInOutCDController()
        projectCDController = ProjectsCDController()
        tasksCDController = TasksCDController()
        taskTimeCDController = TasksTimeCDController()
        
        // Disable display mode if ios less than 12.0.
        if #available(iOS 13.0, *) {
            btnDisplayMode.isHidden = false
        }
        else {
            nsLBtnDisModeHeight.constant = 0
            btnDisplayMode.isHidden = true
        }
    }
    
    func setUpEmailPhone() {
        if let strEmail = UserDefaults.standard.string(forKey: "userEmail") {
            txtEmailPhone.text = strEmail
        }
        else {
            txtEmailPhone.text = ""
        }
        if let strPhone = UserDefaults.standard.string(forKey: "phoneNo") {
            txtEmailPhone.text = "\(txtEmailPhone.text!)  |  \(strPhone)"
            txtPhone.text = strPhone
        }
    }
    
    func gestureRecognizer(_ gestureRecognizer: UIGestureRecognizer, shouldReceive
        touch: UITouch) -> Bool {
        if touch.view == viewButtons || touch.view == viewBG {
            return true
        }
        return false
    }
    
    func updateViewsAndColor() {
        // Update colors.
        if UIScreen.main.bounds.height > 600 {
            self.view.addGradient(cgPStart: CGPoint(x: 0, y: 0), cgPEnd: CGPoint(x: 1, y: 0.6))
        }
        else {
            self.view.addGradient(cgPStart: CGPoint(x: 0, y: 0), cgPEnd: CGPoint(x: 1, y: 0.7))
        }
        scrollView.layer.borderColor = g_colorMode.textColor().cgColor
        scrollView.layer.borderWidth = 0.3
        btnChangePswd.setTitleColor(g_colorMode.textColor(), for: .normal)
        btnLogout.setTitleColor(g_colorMode.textColor(), for: .normal)
        btnDisplayMode.setTitleColor(g_colorMode.textColor(), for: .normal)
        btnAbout.setTitleColor(g_colorMode.textColor(), for: .normal)
        btnMultiTask.setTitleColor(g_colorMode.textColor(), for: .normal)
        btnResetIntro.setTitleColor(g_colorMode.textColor(), for: .normal)
        tblDisplayModes.layer.borderColor = g_colorMode.lineColor().cgColor
        lblAccSetting.textColor = .lightGray
        lblAppSetting.textColor = .lightGray
        scrollView.backgroundColor = g_colorMode.defaultColor()
        viewAccount.backgroundColor = g_colorMode.defaultColor()
        viewApp.backgroundColor = g_colorMode.defaultColor()
        view.backgroundColor = g_colorMode.defaultColor()
        btnChangeImage.backgroundColor = g_colorMode.midColor()
        btnChangeImage.setImage(#imageLiteral(resourceName: "camera"), for: .normal)
        btnChangeImage.layer.borderWidth = 1
        imgVProfile.layer.borderWidth = 1
        imgVProfile.layer.borderColor = UIColor.white.cgColor
        btnChangeImage.layer.borderColor = UIColor.white.cgColor
    }
    
    @IBAction func btnChangePswdPressed(_ sender: Any) {
        performSegue(withIdentifier: "SegueToResetPw", sender: nil)
    }
    
    func openCamera()
    {
        if UIImagePickerController.isSourceTypeAvailable(UIImagePickerController.SourceType
            .camera) {
            let imagePicker = UIImagePickerController()
            imagePicker.delegate = self
            imagePicker.sourceType = UIImagePickerController.SourceType.camera
            imagePicker.allowsEditing = false
            self.present(imagePicker, animated: true, completion: nil)
        }
        else
        {
            let alert  = UIAlertController(title: "Warning", message: "You don't have camera"
                , preferredStyle: .alert)
            alert.addAction(UIAlertAction(title: "OK", style: .default, handler: nil))
            self.present(alert, animated: true, completion: nil)
        }
    }
    
    /// To open image picker view.
    func openGallary()
    {
        if UIImagePickerController.isSourceTypeAvailable(UIImagePickerController.SourceType
            .photoLibrary){
            actIndicator.startAnimating()
            let imagePicker = UIImagePickerController()
            imagePicker.delegate = self
            imagePicker.allowsEditing = true
            imagePicker.sourceType = UIImagePickerController.SourceType.photoLibrary
            self.present(imagePicker, animated: true, completion: nil)
        }
        else
        {
            let alert  = UIAlertController(title: "Warning"
                , message: "You don't have permission to access gallery.", preferredStyle: .alert)
            alert.addAction(UIAlertAction(title: "OK", style: .default, handler: nil))
            self.present(alert, animated: true, completion: nil)
        }
    }
    
    func imagePickerController(_ picker: UIImagePickerController, didFinishPickingMediaWithInfo
        info: [UIImagePickerController.InfoKey : Any]) {
        if (info[UIImagePickerController.InfoKey.editedImage] as? UIImage) != nil {
            imgVProfile.image = info[UIImagePickerController.InfoKey.editedImage] as? UIImage
            self.btnEditSave.alpha = 1
            self.btnEditSave.isUserInteractionEnabled = true
        }
        picker.dismiss(animated: true, completion: nil)
        self.actIndicator.stopAnimating()
    }
    
    func imagePickerControllerDidCancel(_ picker: UIImagePickerController) {
        self.actIndicator.stopAnimating()
    }
    
    @IBAction func btnChangeImagePressed(_ sender: Any) {
        let alert = UIAlertController(title: "Choose Image", message: nil, preferredStyle:
            .actionSheet)
        alert.addAction(UIAlertAction(title: "Camera", style: .default, handler: { _ in
            self.openCamera()
        }))
        
        alert.addAction(UIAlertAction(title: "Gallery", style: .default, handler: { _ in
            self.openGallary()
        }))
        
        alert.addAction(UIAlertAction.init(title: "Cancel", style: .cancel, handler: nil))
        
        self.present(alert, animated: true, completion: nil)
    }
    
    @IBAction func btnEditProfilePressed(_ sender: Any) {
        if btnEditSave.currentTitle == "Edit" {
            nsLScrollViewTop.constant = scrollView.frame.maxY
            nsLtxtFCenter.constant = -150
            nsLTxtFEmailTop.constant = 30
            nsLTxtUsernameTop.constant = 30
            UIView.animate(withDuration: 0.3, animations: {
                self.view.layoutIfNeeded()
            })
            if let strEmail = UserDefaults.standard.string(forKey: "userEmail") {
                txtEmailPhone.text = strEmail
            }
            self.txtEmailPhone.font = txtEmailPhone.font?.withSize(15)
            self.txtUsername.useUnderline()
            self.txtPhone.useUnderline()
            self.txtUsername.isUserInteractionEnabled = true
            self.txtPhone.isUserInteractionEnabled = true
            self.txtEmailPhone.textAlignment = .left
            self.txtUsername.textAlignment = .left
            self.btnChangeImage.isHidden = false
            self.btnEditSave.setTitle("Save", for: .normal)
            self.btnCancel.isHidden = false
            self.btnEditSave.alpha = 0.5
            self.btnEditSave.isUserInteractionEnabled = false
            self.view.layer.sublayers?.removeFirst()
            self.view.addGradient(cgPStart: CGPoint(x: 0, y: 0), cgPEnd: CGPoint(x: 1, y: 1))
        }
        else {
            
        }
    }
    
    @IBAction func btnCancelPressed(_ sender: Any) {
        nsLtxtFCenter.constant = 0
        nsLTxtFEmailTop.constant = 0
        nsLTxtUsernameTop.constant = 8
        nsLScrollViewTop.constant = 15
        UIView.animate(withDuration: 0.3, animations: {
            self.view.layoutIfNeeded()
            self.view.layer.sublayers?.removeFirst()
            if UIScreen.main.bounds.height > 600 {
                self.view.addGradient(cgPStart: CGPoint(x: 0, y: 0), cgPEnd: CGPoint(x: 1, y: 0.6))
            }
            else {
                self.view.addGradient(cgPStart: CGPoint(x: 0, y: 0), cgPEnd: CGPoint(x: 1, y: 0.7))
            }
        })
        setUpEmailPhone()
        self.txtUsername.layer.sublayers?.removeFirst()
        self.txtEmailPhone.font = txtEmailPhone.font?.withSize(10)
        self.txtUsername.isUserInteractionEnabled = false
        self.txtPhone.isUserInteractionEnabled = false
        self.txtEmailPhone.textAlignment = .center
        self.txtUsername.textAlignment = .center
        self.btnChangeImage.isHidden = true
        self.btnEditSave.setTitle("Edit", for: .normal)
        self.btnCancel.isHidden = true
        self.btnEditSave.alpha = 1
        self.btnEditSave.isUserInteractionEnabled = true
    }
    
    @objc func imgProfileTapped(sender: UITapGestureRecognizer) {
        let imageView = sender.view as! UIImageView
        let imgView = UIImageView(image: imageView.image)
        imgView.frame = CGRect.zero
        imgView.center = imgVProfile.center
        imgView.contentMode = .scaleAspectFit
        imgView.isUserInteractionEnabled = true
        let tap = UITapGestureRecognizer(target: self, action: #selector(dismissFullscreenImage(sender:)))
        imgView.addGestureRecognizer(tap)
        self.view.addSubview(imgView)
        self.tabBarController?.tabBar.isHidden = true
        UIView.animate(withDuration: 0.3, animations: {
            imgView.frame = UIScreen.main.bounds
            imgView.backgroundColor = UIColor.black.withAlphaComponent(0.8)
        })
    }
    
    @objc func dismissFullscreenImage(sender: UITapGestureRecognizer) {
        self.tabBarController?.tabBar.isHidden = false
        sender.view?.removeFromSuperview()
    }
    
    @IBAction func txtUsernamePrimaryAction(_ sender: Any) {
        txtPhone.becomeFirstResponder()
    }
    
    @IBAction func txtPhonePrimaryAction(_ sender: Any) {
        txtPhone.endEditing(true)
    }
    
    @IBAction func txtUsernameChanged(_ sender: Any) {
        self.btnEditSave.alpha = 1
        self.btnEditSave.isUserInteractionEnabled = true
    }
    
    @IBAction func txtPhoneChanged(_ sender: Any) {
        self.btnEditSave.alpha = 1
        self.btnEditSave.isUserInteractionEnabled = true
    }
    
    @IBAction func btnLogoutPressed(_ sender: Any) {
        
        //User wants log out.
        if tasksCDController.isSynched() {
            showLogoutAlert()
        }
        else {
            showAlertInernetConnection()
        }
    }
    
    func showTableView() {
        tblDisplayModes.reloadData()
        viewBG.isHidden = false
        if nsLDisModeHeight.constant == 0 {
            nsLDisModeHeight.constant = cgFTableDisHeight
            UIView.animate(withDuration: 0.2) {
                self.view.layoutIfNeeded()
            }
        }
    }
    
    @IBAction func btnDisplayModePressed(_ sender: Any) {
        tableSelection = .displayMode
        showTableView()
    }
    
    @IBAction func btnMultiTaskPressed(_ sender: Any) {
        tableSelection = .multiTask
        showTableView()
    }
    
    @IBAction func btnAboutPressed(_ sender: Any) {
        performSegue(withIdentifier: "SegueToAbout", sender: nil)
    }
    
    @IBAction func btnResetIntroPressed(_ sender: Any) {
        let refreshAlert = UIAlertController(title: "Intro Page Reset"
            , message: "Are you sure want to reset introduction page view?"
            , preferredStyle: UIAlertController.Style.alert)
        
        refreshAlert.addAction(UIAlertAction(title: "Yes", style: .default
            , handler: { (action: UIAlertAction!) in
                // Reset flag.
                UserDefaults.standard.setValue(false, forKey: "IntroStatusCell")
                UserDefaults.standard.setValue(false, forKey: "IntroStatusTask")
                UserDefaults.standard.setValue(false, forKey: "IntroStatusWeekLeft")
                UserDefaults.standard.setValue(false, forKey: "IntroStatusDayLeft")
        }))
        refreshAlert.addAction(UIAlertAction(title: "No", style: .cancel, handler: nil))
        present(refreshAlert, animated: true, completion: nil)
    }
    
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        // Display Auto mode if greater than ios 13.
        if tableSelection == .displayMode {
            if #available(iOS 13.0, *) {
                return 3
            }
            else {
                return 2
            }
        }
        else {
            return 2
        }
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "displayModeCell")!
        let label = cell.viewWithTag(1) as! UILabel
        label.textColor = .gray
        
        // Check for initialized cell
        if label.text == "" {
            // Add line.
            cell.contentView.addLine(rect: CGRect(x: 20, y: label.frame.maxY+5
                , width: view.frame.width, height: 1))
        }
        
        let imgSel = cell.viewWithTag(2) as! UIImageView
        imgSel.backgroundColor = g_colorMode.defaultColor()
        imgSel.image = nil
        
        if tableSelection == .displayMode {
            // If selection type is display mode.
            switch indexPath.row {
                case 0 :
                    label.text = "Light"
                    if g_colorMode == .light {
                        imgSel.backgroundColor = g_colorMode.midColor()
                        imgSel.image = #imageLiteral(resourceName: "rightIcon")
                }
                
                case 1 :
                    label.text = "Dark"
                    if g_colorMode == .dark {
                        imgSel.backgroundColor = g_colorMode.midColor()
                        imgSel.image = #imageLiteral(resourceName: "rightIcon")
                }
                
                default:
                    label.text = "Auto"
                    if g_colorMode == .auto {
                        imgSel.backgroundColor = g_colorMode.midColor()
                        imgSel.image = #imageLiteral(resourceName: "rightIcon")
                }
            }
        }
        else {
            // If selection type is display mode.
            if indexPath.row == 0 {
                label.text = "Enable"
                if UserDefaults.standard.object(forKey: "multi_task") as! Bool {
                    imgSel.backgroundColor = g_colorMode.midColor()
                    imgSel.image = #imageLiteral(resourceName: "rightIcon")
                }
            }
            else {
                label.text = "Disable"
                if !(UserDefaults.standard.object(forKey: "multi_task") as! Bool) {
                    imgSel.backgroundColor = g_colorMode.midColor()
                    imgSel.image = #imageLiteral(resourceName: "rightIcon")
                }
            }
        }
        return cell
    }
    
    func tableView(_ tableView: UITableView, heightForHeaderInSection section: Int) -> CGFloat {
        if tableSelection == .multiTask {
            return 50
        }
        return 40
    }
    
    func tableView(_ tableView: UITableView, viewForHeaderInSection section: Int) -> UIView? {
        let header = TableHeaderView()
        if tableSelection == .displayMode {
            header.customInit(title: "Display mode", section: section)
        }
        else {
            header.customInit(title: "Multi task", section: section)
            header.lblHint.text = "If you disable multi task, you can run only one task at a time."
            header.lblHint.isHidden = false
            header.imgHint.isHidden = false
        }
        header.lblTitle.center = CGPoint(x: header.lblTitle.frame.midX, y: 20)
        header.contentView.backgroundColor = g_colorMode.defaultColor()
        header.btnFilter.isHidden = true
        return header
    }
    
    func tableView(_ tableView: UITableView, willDisplayHeaderView view: UIView,
                   forSection section: Int) {
        view.tintColor = UIColor.clear
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        if tableSelection == .displayMode {
            let prevColorMode = g_colorMode
            switch indexPath.row {
                case 0 :
                    UserDefaults.standard.setValue(1, forKey: "colorMode")
                
                case 1 :
                    UserDefaults.standard.setValue(2, forKey: "colorMode")
                
                case 2:
                    UserDefaults.standard.setValue(0, forKey: "colorMode")
                
                default : break
            }
            setColorMode()
            
            // If color mode changed, update view.
            if g_colorMode != prevColorMode {
                updateDisplayMode()
            }
            hideDisplayMode()
        }
        else {
            if indexPath.row == 0 {
                UserDefaults.standard.set(true, forKey: "multi_task")
                hideDisplayMode()
            }
            else {
                // Check for multiple task running
                let userActivityVC = self.tabBarController?.viewControllers![1] as! UserActivityVC
                if userActivityVC.arrRunningTask.count > 1 {
                    showMultitaskAlert()
                }
                else {
                    UserDefaults.standard.set(false, forKey: "multi_task")
                    hideDisplayMode()
                }
            }
        }
    }
    
    /// To hide table view and refresh table view.
    func hideTableAndRefresh() {
        tblDisplayModes.reloadData()
        viewBG.isHidden = true
        nsLDisModeHeight.constant = 0
        UIView.animate(withDuration: 0.2) {
            self.view.layoutIfNeeded()
        }
    }
    
    func showMultitaskAlert() {
        let alert = UIAlertController(title: "Alert..!", message:
            "Currently multiple tasks are running. Do you want to stop it?"
            , preferredStyle: UIAlertController.Style.alert)
        alert.addAction(UIAlertAction(title: "Cancel", style: UIAlertAction.Style.cancel
            , handler: { _ in
        }))
        alert.addAction(UIAlertAction(title: "Yes", style: UIAlertAction.Style.default
            , handler: { _ in
                UserDefaults.standard.set(false, forKey: "multi_task")
                // Stop currently running tasks except one task.
                let userActivityVC = self.tabBarController?.viewControllers![1] as! UserActivityVC
                userActivityVC.stopRunningTask(stopAll: true, completion: {
                    userActivityVC.updateProject()
                    self.hideDisplayMode()
                })
        }))
        self.present(alert, animated: true, completion: nil)
    }
    
    /// To handle pan gesture to tableview display mode selector.
    @objc func panToTableview(panGesture: UIPanGestureRecognizer) {
        let translation = panGesture.translation(in: self.view)
        if tblDisplayModes.frame.minY + translation.y >= cgFTableDisHeight {
            nsLDisModeHeight.constant = cgFTableDisHeight - translation.y
            let progress = (nsLDisModeHeight.constant) / (cgFTableDisHeight)
            viewBG.alpha = progress
        }
        if panGesture.state == .ended || panGesture.state == .cancelled ||
            panGesture.state == .failed {
            // If table moved below to more than half of its height.
            if nsLDisModeHeight.constant < cgFTableDisHeight/2 || panGesture.velocity(in: view).y > 500 {
                // Dismiss view.
                nsLDisModeHeight.constant = 0
                UIView.animate(withDuration: 0.2, animations: {
                    self.view.layoutIfNeeded()
                }) { _ in
                    self.viewBG.alpha = 1
                    self.viewBG.isHidden = true
                }
            }
            else {
                nsLDisModeHeight.constant = cgFTableDisHeight
                UIView.animate(withDuration: 0.5, animations: {
                    self.view.layoutIfNeeded()
                    self.viewBG.alpha = 1
                })
            }
        }
    }
    
    func updateDisplayMode() {
        let tabBarCtrlr = self.tabBarController as! TabBarController
        tabBarCtrlr.updateAllViewCtrlrs()
    }
    
    @IBAction func viewPressed(_ sender: Any) {
        hideDisplayMode()
    }
    
    @objc func viewBtnsPressed(_ sender: Any) {
        hideDisplayMode()
    }
    
    /// Background view pressed.
    @objc func viewBGPressed(_ sender: Any) {
        hideDisplayMode()
    }
    
    func hideDisplayMode() {
        viewBG.isHidden = true
        if nsLDisModeHeight.constant != 0 {
            nsLDisModeHeight.constant = 0
            UIView.animate(withDuration: 0.2) {
                self.view.layoutIfNeeded()
            }
        }
        self.view.endEditing(true)
    }
    
    deinit {
        print("Account view deinitialised")
    }
    
    /// Removes all delegates.
    func resetViews() {
        //Log out action
        UserDefaults.standard.removeObject(forKey: "username")
        UserDefaults.standard.removeObject(forKey: "userAuthKey")
        UserDefaults.standard.removeObject(forKey: "userId")
        UserDefaults.standard.removeObject(forKey: "userEmail")
        UserDefaults.standard.removeObject(forKey: "profileUrl")
        UserDefaults.standard.removeObject(forKey: "phoneNo")
        g_dictProjectDetails = nil
        g_arrCTaskDetails = nil
        g_userProfile = nil
        g_taskPageNo = 1
        g_loginPageNo = 1
        g_isPunchedIn = false
        g_isPunchedOut = false
        
        punchInOutCDController.deleteAllData()
        tasksCDController.deleteAllData()
        projectCDController.deleteAllData()
        
        let userActivityVC = self.tabBarController?.viewControllers![1] as! UserActivityVC
        if let tmr = userActivityVC.timer {
            // If timer running invalidate it.
            tmr.invalidate()
        }
        NotificationCenter.default.removeObserver(userActivityVC)
        if let viewActivity = self.tabBarController?.viewControllers![0] as?
            MyActivityViewController {
            guard nil != viewActivity.arrActView else {
                return
            }
            for actView in viewActivity.arrActView {
                actView.delegate = nil
                actView.delegateChart = nil
                actView.calendarView.delegateChart = nil
                actView.calendarView.delegate = nil
            }
        }
    }
    
    func showAlertInernetConnection() {
        //If user tries to logout ihn the break time shows alert.
        let alert = UIAlertController(title: "Alert..!", message:
            "Please connect to internet before logout..! (Your tasks details not synched with server)"
            , preferredStyle: UIAlertController.Style.alert)
        alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default,
                                      handler: { _ in
        }))
        self.present(alert, animated: true, completion: nil)
    }
    
    func showAlertInBreak() {
        //If user tries to logout ihn the break time shows alert.
        let alert = UIAlertController(title: "Alert..!", message:
            "Your in a break. Do you want continue?", preferredStyle: UIAlertController.Style.alert)
        alert.addAction(UIAlertAction(title: "Cancel", style: UIAlertAction.Style.default,
                                      handler: { _ in
                                        
        }))
        alert.addAction(UIAlertAction(title: "Continue", style: UIAlertAction.Style.default,
                                      handler: { _ in
                                        self.showLogoutAlert()
        }))
        self.present(alert, animated: true, completion: nil)
    }
    
    func showLogoutAlert() {
        //Alert while log out.
        let alert = UIAlertController(title: "Log out?", message: "Please select an option",
            preferredStyle: UIAlertController.Style.alert)
        alert.addAction(UIAlertAction(title: "Log out", style: UIAlertAction.Style.default,
            handler: {(_: UIAlertAction!) in
                // self.taskUpdater.deleteAllData()
                // self.userActUpdater.deleteAllData()
                if let viewLogin = (UIApplication.shared.windows.first!.rootViewController as? UINavigationController)?.viewControllers.first as?
                    LoginViewController {
                    viewLogin.updateGradient()
                    viewLogin.nSleepTime = 0
                    viewLogin.txtFEmail.text = ""
                    viewLogin.txtFPassword.text = ""
                }
                self.resetViews()
                self.presentingViewController?.dismiss(animated: true, completion: nil)
            }
        ))
        if true != g_isPunchedOut {
            alert.addAction(UIAlertAction(title: "Punch out"
                , style: UIAlertAction.Style.default, handler: {(_: UIAlertAction!) in
                    //Office Leave action
                    let userActivityVC = self.tabBarController?.viewControllers![1]
                        as! UserActivityVC
                    userActivityVC.stopRunningTask(stopAll: true, completion: {
                        userActivityVC.showPunchOutAlert()
                    })
            }))
        }
        alert.addAction(UIAlertAction(title: "Cancel", style: UIAlertAction.Style.cancel, handler:
            { _ in
                //Cancel Action
        }))
        self.present(alert, animated: true, completion: nil)
    }
    
    func updatePunchOutTime() {
        actIndicator.startAnimating()
        APIResponseHandler.stopTaskOrPunchIn(completion: {
            status in
            if status {
                print("Punch out time updated..!")
//                self.resetViews()
//                if let viewLogin = self.presentingViewController as? LoginViewController {
//                    viewLogin.nSleepTime = 0
//                }
//                self.presentingViewController?.dismiss(animated: true, completion: {
//                    self.view = nil
//                })
                g_isPunchedOut = true
            }
            else {
                print("Error while updating punch out time..!")
            }
            self.actIndicator.stopAnimating()
        })
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
            // Remove old gradients.
            self.view.layer.sublayers?.removeFirst()
            
            setColorMode()
            updateGradient()
        }
    }
    
    func updateGradient() {
        if let tabController = self.navigationController?.viewControllers[0] as? TabBarController {
            tabController.updateAllViewCtrlrs()
        }
        
        if UIScreen.main.bounds.height > 600 {
            self.view.addGradient(cgPStart: CGPoint(x: 0, y: 0), cgPEnd: CGPoint(x: 1, y: 0.5))
        }
        else {
            self.view.addGradient(cgPStart: CGPoint(x: 0, y: 0), cgPEnd: CGPoint(x: 1, y: 0.6))
        }
        view.layer.needsLayout()
    }
}
