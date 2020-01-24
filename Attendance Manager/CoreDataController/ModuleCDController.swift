/*//////////////////////////////////////////////////////////////////////////////
 //
 //    Copyright (c) GreenPrint Technologies LLC. 2019
 //
 //    File Name         : ModuleCDController.swift
 //
 //    File Created      : 12:Dec:2019
 //
 //    Dev Name          : Sachin Kumar K.
 //
 //    Description       : Module entity Core data hndler.
 //
 //////////////////////////////////////////////////////////////////////////// */

import Foundation
import UIKit
import CoreData

class ModuleCDController {
    var nsMOForUserTimes: NSManagedObject!
    var nsManagedContext: NSManagedObjectContext!
    init() {
        guard let appDelegate = UIApplication.shared.delegate as? AppDelegate else { return }
        nsManagedContext = appDelegate.persistentContainer.viewContext
    }
    
    /// Commit database.
    func saveContext() {
        do {
            try nsManagedContext.save()
        } catch let error as NSError {
            print("Could not save. \(error), \(error.userInfo)")
        }
    }
    
    /// Add new projects to core data.
    func addOrUpdateModule(modId: Int, modName: String, projId: Int) {
        // Add or update module.
        if !isModuleExist(modId: modId, projId: projId) {
            let userEntity = NSEntityDescription.entity(forEntityName: "Modules",
                                                        in: nsManagedContext)!
            nsMOForUserTimes = NSManagedObject(entity: userEntity, insertInto: nsManagedContext)
            nsMOForUserTimes.setValuesForKeys(["module_id": modId, "name": modName,
                                               "project_id": projId])
        }
        else {
            let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Modules")
            fetchRequest.predicate = NSPredicate(format: "module_id = %d", modId)
            let res = try! nsManagedContext.fetch(fetchRequest)
            if res.count > 0 {
                let nsMObject = res[0] as! NSManagedObject
                nsMObject.setValue(modName, forKey: "name")
                nsMObject.setValue(projId, forKey: "project_id")
            }
        }
        saveContext()
    }
    
    /// get project name from project id.
    func getModuleIdAndNames(projId: Int) -> Dictionary<Int, String> {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Modules")
        fetchRequest.predicate = NSPredicate(format: "project_id = %d", projId)
        var dictModules = Dictionary<Int, String>()
        do {
            let nsMContext = try nsManagedContext.fetch(fetchRequest)
            for nsMObject in nsMContext {
                let modName = (nsMObject as! NSManagedObject).value(forKey: "name") as! String
                let modId = (nsMObject as! NSManagedObject).value(forKey: "module_id") as! Int
                dictModules.updateValue(modName, forKey: modId)
            }
        }
        catch {
            print("Error")
        }
        return dictModules
    }
    
    /// get project name from project id.
    func getModuleId(module name: String, projId: Int) -> Int {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Modules")
        fetchRequest.predicate = NSPredicate(format: "name = %@ AND project_id = %d", name, projId)
        var modId: Int!
        do {
            let nsMContext = try nsManagedContext.fetch(fetchRequest)
            let nsMObject = nsMContext[0] as! NSManagedObject
            modId = (nsMObject.value(forKey: "module_id") as! Int)
        }
        catch {
            print("Error")
        }
        return modId
    }
    
    /// returns true if project exists.
    func isModuleExist(modId: Int, projId: Int) -> Bool {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Modules")
        fetchRequest.predicate = NSPredicate(format: "module_id = %d AND project_id = %d", modId,
                                             projId)
        let res = try! nsManagedContext.fetch(fetchRequest)
        return res.count > 0 ? true : false
    }
    
    /// Deletes all the data in Modules entiy.
    func deleteAllData() {
        let fetchRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Modules")
        fetchRequest.returnsObjectsAsFaults = false
        do {
            let results = try nsManagedContext.fetch(fetchRequest)
            for object in results {
                guard let objectData = object as? NSManagedObject else {continue}
                nsManagedContext.delete(objectData)
                saveContext()
            }
        }
        catch {
            print("Error")
        }
    }
}

