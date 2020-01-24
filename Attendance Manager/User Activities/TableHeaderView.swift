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

import UIKit

class TableHeaderView: UITableViewHeaderFooterView {
    /// Header section count.
    var nSection: Int!
    /// Header label in header view.
    var lblTitle: UILabel!
    var btnFilter: UIButton!
    var lblFilterIndicator: UILabel!
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

        // Set constraints to filter button.
        cgRValue = CGRect(x: cgFScreenWidth - 64, y: lblTitle.frame.minY - 12, width: 44,
                          height: 44)
        btnFilter = UIButton(frame: cgRValue)
        btnFilter.backgroundColor = .clear
        btnFilter.setImage(#imageLiteral(resourceName: "FilterIcon"), for: .normal)
        btnFilter.imageEdgeInsets = UIEdgeInsets(top: 14, left: 12, bottom: 14, right: 16)
        self.addSubview(btnFilter)
        
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
}
