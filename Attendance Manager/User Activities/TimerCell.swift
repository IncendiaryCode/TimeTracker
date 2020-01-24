/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : TimerCell.swift
 //
 //    File Created      : 26:Dec:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : Timer cell.
 //
 //////////////////////////////////////////////////////////////////////////// */

import UIKit

class TimerCell: UICollectionViewCell {
    @IBOutlet weak var lblTimer: UILabel!
    @IBOutlet weak var lblStartTime: UILabel!
    @IBOutlet weak var lblTaskTitle: UILabel!
    
    var timer: Timer? = Timer()
    var nTotalTime: Int!
    
    override func awakeFromNib() {
        super.awakeFromNib()
    }
    
    func customInit(taskId: Int) {
        let strStartedOn = getTaskStartTime(taskId: taskId)
        let strTaskName = getTaskName(taskId: taskId)
        lblTaskTitle.text = strTaskName
        lblStartTime.text = strStartedOn
        nTotalTime = getTaskTotalTime(taskId: taskId)
        lblTimer.text = "\(getSecondsToHoursMinutesSeconds(seconds: self.nTotalTime))"
        runTime()
    }
    
    func customInitPuncher() {
        let punchInOutCDController = PunchInOutCDController()
        let strTaskName = "Punch In"
        lblTaskTitle.text = strTaskName
        if g_isPunchedIn ?? false {
            nTotalTime = punchInOutCDController.getTotalTime()
            let nStart = punchInOutCDController.getLoginTime()
            let strTime = getSecondsToHoursMinutesSecondsWithAllFields(seconds: nStart)
            let str12HrTime = convert24to12Format(strTime: strTime)
            let strStartedOn = "Started at \(str12HrTime)"
            lblStartTime.text = strStartedOn
            lblTimer.text = "\(getSecondsToHoursMinutesSeconds(seconds: self.nTotalTime))"
            
            if !(g_isPunchedOut) {
                // run timer only if punchout not updated.
                runTime()
            }
            else {
                lblTaskTitle.text = "Punched out"
            }
        }
        else {
            lblStartTime.text = "Not started"
            lblTimer.text = "00:00:00"
        }
    }
    
    /// To start timer object
    func runTime() {
        timer = Timer.scheduledTimer(timeInterval: 1, target: self, selector:
            #selector(timerAction), userInfo: nil, repeats: true)
        RunLoop.current.add(self.timer!, forMode: RunLoop.Mode.common)
    }
    
    @objc func timerAction() {
        //Update counter label.
        self.nTotalTime += 1
        lblTimer.text = "\(getSecondsToHoursMinutesSeconds(seconds: self.nTotalTime))"
    }
}
