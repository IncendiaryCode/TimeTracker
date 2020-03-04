/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : TimingsCell.swift
 //
 //    File Created      : 08:Nov:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : Timing cell in edit task view.
 //
 //////////////////////////////////////////////////////////////////////////// */

protocol DateTimeCellDelegate {
    func removeAddSelected(indexPath: IndexPath)
    func removeConfirmSelected(indexPath: IndexPath)
    func dateSelected(indexPath: IndexPath)
    func startTimeSelected(indexPath: IndexPath)
    func endTimeSelected(indexPath: IndexPath)
    func endEditingDescr(indexPath: IndexPath)
    func startEditingDescr(indexPath: IndexPath)
}

import UIKit

class TimingsCell: UITableViewCell {
    @IBOutlet weak var lblDate: UILabel!
    @IBOutlet weak var lblStartTime: UILabel!
    @IBOutlet weak var lblEndTime: UILabel!
    @IBOutlet weak var btnRemoveAdd: UIButton!
    @IBOutlet weak var nsLLeadeingSpace: NSLayoutConstraint!
    @IBOutlet weak var btnRemoveConfirm: UIButton!
    @IBOutlet weak var txtFDescription: UITextField!
    
    var indexPath: IndexPath!
    var timeId: Int?
    var delegate: DateTimeCellDelegate?
    var viewSeparator: UIView!
    override func awakeFromNib() {
        super.awakeFromNib()
        
        var tap = UITapGestureRecognizer(target: self, action: #selector(self
            .lblDatePressed(sender:)))
        lblDate.isUserInteractionEnabled = true
        lblDate.addGestureRecognizer(tap)
        
        tap = UITapGestureRecognizer(target: self, action: #selector(self
            .lblStartTimePressed(sender:)))
        lblStartTime.isUserInteractionEnabled = true
        lblStartTime.addGestureRecognizer(tap)
        
        tap = UITapGestureRecognizer(target: self, action: #selector(self
            .lblEndTimePressed(sender:)))
        lblEndTime.isUserInteractionEnabled = true
        lblEndTime.addGestureRecognizer(tap)
        
        lblDate.addRightSideLine()
        lblStartTime.addRightSideLine()
        txtFDescription.tintColor = g_colorMode.tintColor()
        txtFDescription.addLine(rect: CGRect(x: 0, y: txtFDescription.bounds.maxY+2
            , width: txtFDescription.bounds.width, height: 1))
        btnRemoveConfirm.setTitleColor(g_colorMode.textColor(), for: .normal)
        
        viewSeparator = UIView(frame: CGRect(x: 0, y: self.lblDate.frame.minY-10
            , width: self.bounds.width, height: 0.5))
        viewSeparator.backgroundColor = g_colorMode.lineColor().withAlphaComponent(0.5)
        self.addSubview(viewSeparator)
    }

    @objc func lblDatePressed(sender: UITapGestureRecognizer) {
         delegate?.dateSelected(indexPath: self.indexPath)
    }
    
    @IBAction func btnRemoveConfirmPressed(_ sender: Any) {
        delegate?.removeConfirmSelected(indexPath: self.indexPath)
    }
    
    @objc func lblStartTimePressed(sender: UITapGestureRecognizer) {
        delegate?.startTimeSelected(indexPath: self.indexPath)
    }
    
    @objc func lblEndTimePressed(sender: UITapGestureRecognizer) {
        delegate?.endTimeSelected(indexPath: self.indexPath)
    }
    
    @IBAction func btnRemoveAddPressed(_ sender: Any) {
        delegate?.removeAddSelected(indexPath: self.indexPath)
    }
    
    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)
        // Configure the view for the selected state
    }
    
    @IBAction func txtFDescrptnExitEditing(_ sender: Any) {
        delegate?.endEditingDescr(indexPath: self.indexPath)
    }
    
    @IBAction func txtFDescptnStartEditing(_ sender: Any) {
        delegate?.startEditingDescr(indexPath: self.indexPath)
    }
    
    @IBAction func txtFDescrStartEditing(_ sender: Any) {
        delegate?.startEditingDescr(indexPath: self.indexPath)
    }
}
