/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : TableHeaderView.swift
 //
 //    File Created      : 09:Sept:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : Header view for user activity table view.
 //
 //////////////////////////////////////////////////////////////////////////// */

protocol FilterToday {
    func switchChanged(to value: Bool)
}

import UIKit

class TableHeaderView: UITableViewHeaderFooterView {
    /// Header section count.
    var nSection: Int!
    /// Header label in header view.
    var lblTitle: UILabel!
    /// Hint in the header view.(By default hidden)
    var lblHint: UILabel!
    /// Hint image
    public var imgHint: UIImageView!
    var btnFilter: UIButton!
    var lblFilterIndicator: UILabel!
    var switchFilter: UISwitch!
    var lblTitleSwitch: UILabel!
    /// Delegate to switch.
    var delegate: FilterToday?
    
    override init(reuseIdentifier: String?) {
        super.init(reuseIdentifier: reuseIdentifier)
    }
    
    required init?(coder aDecoder: NSCoder) {
        fatalError("init(coder:) has not been implemented")
    }
        
    func customInit(title: String, section: Int) {
        // Set header label constraints.
        var cgRValue: CGRect!
        let cgFScreenWidth = UIScreen.main.bounds.width
        cgRValue = CGRect(x: cgFScreenWidth / 3, y: 62, width: cgFScreenWidth / 2 -
            cgFScreenWidth / 6, height: 21)
        lblTitle = UILabel(frame: cgRValue)
        lblTitle.textAlignment = .center
        lblTitle.textColor = .lightGray
        lblTitle.font = lblTitle.font.withSize(12)
        self.lblTitle.text = title
        self.addSubview(lblTitle)
        
        // Setup hint text.
        cgRValue = CGRect(x: 35, y: lblTitle.bounds.maxY+10, width: cgFScreenWidth-60, height: 15)
        lblHint = UILabel(frame: cgRValue)
        lblHint.textColor = UIColor.lightGray.withAlphaComponent(0.7)
        lblHint.font = lblHint.font.withSize(10)
        lblHint.isHidden = true
        self.addSubview(lblHint)
        
        // Setup hint image.
        cgRValue = CGRect(x: 20, y: lblTitle.bounds.maxY+10, width: 10, height: 10)
        imgHint = UIImageView(frame: cgRValue)
        imgHint.center = CGPoint(x: imgHint.center.x, y: lblHint.center.y)
        imgHint.alpha = 0.5
        imgHint.image = #imageLiteral(resourceName: "AboutIcon")
        imgHint.isHidden = true
        self.addSubview(imgHint)
        
        // Set constraints to filter button.
        cgRValue = CGRect(x: cgFScreenWidth - 64, y: lblTitle.frame.minY - 12, width: 44,
                          height: 44)
        btnFilter = UIButton(frame: cgRValue)
        btnFilter.backgroundColor = .clear
        btnFilter.setImage(#imageLiteral(resourceName: "FilterIcon"), for: .normal)
        btnFilter.imageEdgeInsets = UIEdgeInsets(top: 14, left: 12, bottom: 14, right: 16)
        self.addSubview(btnFilter)
        
        // Setup switch.
        cgRValue = CGRect(x: cgFScreenWidth - 110, y: lblTitle.frame.minY, width: 50, height: 44)
        switchFilter = UISwitch(frame: cgRValue)
        switchFilter.transform = CGAffineTransform(scaleX: 0.75, y: 0.75)
        switchFilter.onTintColor = UIColor(red: 181/255, green: 108/255,
                                           blue: 249/255, alpha: 1.0)
        switchFilter.center = CGPoint(x: switchFilter.center.x, y: btnFilter.center.y)
        switchFilter.isHidden = true
        switchFilter.addTarget(self, action: #selector(switchChanged), for: UIControl.Event
            .valueChanged)
        self.addSubview(switchFilter)
        
        // Setup title for switch.
        cgRValue = CGRect(x: switchFilter.frame.minX, y: switchFilter.frame.maxY
            , width: switchFilter.bounds.width+20, height: 15)
        lblTitleSwitch = UILabel(frame: cgRValue)
        lblTitleSwitch.text = "Today"
        lblTitleSwitch.center = CGPoint(x: switchFilter.center.x, y: lblTitleSwitch.center.y)
        lblTitleSwitch.textColor = .lightGray
        lblTitleSwitch.textAlignment = .center
        lblTitleSwitch.font = lblTitleSwitch.font.withSize(10)
        lblTitleSwitch.isHidden = true
        addSubview(lblTitleSwitch)
        
        self.nSection = section // Optional..! To create multiple header

        // Add constraints to filter indicator view.
        cgRValue =  CGRect(x: cgFScreenWidth - 60, y: lblTitle.frame.minY, width: 5, height: 5)
        lblFilterIndicator = UILabel(frame: cgRValue)
        lblFilterIndicator.layer.masksToBounds = true
        lblFilterIndicator.layer.cornerRadius = 2.5
        lblFilterIndicator.backgroundColor = .clear
        self.addSubview(lblFilterIndicator)
    
        // Add Bottom line to view.
//        self.layer.backgroundColor = UIColor.white.cgColor
//        self.layer.masksToBounds = false
//        self.layer.shadowColor = UIColor.lightGray.cgColor
//        self.layer.shadowOffset = CGSize(width: 0.0, height: 0.25)
//        self.layer.shadowOpacity = 0.5
//        self.layer.shadowRadius = 2
    }
    
    @objc func switchChanged(switchFilter: UISwitch) {
        delegate?.switchChanged(to: switchFilter.isOn)
    }
}
