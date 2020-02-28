/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : FilterProjectCell.swift
 //
 //    File Created      : 25:Sept:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : Filter table view cell.
 //
 //////////////////////////////////////////////////////////////////////////// */

import UIKit

class FilterProjectCell: UITableViewCell {
    @IBOutlet weak var imgSelect: UIImageView!
    @IBOutlet weak var lblProjectName: UILabel!
    @IBOutlet weak var imgProjectLogo: UIImageView!
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)
        // Configure the view for the selected state
    }

}
