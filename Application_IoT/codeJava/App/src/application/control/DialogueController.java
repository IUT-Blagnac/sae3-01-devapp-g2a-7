package application.control;


import org.json.simple.JSONObject;
import application.view.MainController;
import application.model.Config;
import application.model.JSONReader;
import application.model.JSONWriter;
import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.control.SplitPane;
import javafx.stage.Stage;


public class DialogueController extends Application {

    private MainController mainController;
    
    /**
     * Start the application, this fonction need to be call with Application.launch() function
     * @param primaryStage the main stage of the application
     */
    @Override
    public void start(Stage primaryStage) {
        try {
            // load FXML file
            FXMLLoader loader = new FXMLLoader();
            loader.setLocation(getClass().getResource("controller/mainView.fxml"));

            // load the view
            SplitPane root = loader.load();
            Scene scene = new Scene(root);
            primaryStage.setScene(scene);

            // default properties
            primaryStage.setMinWidth(root.getPrefWidth());
            primaryStage.setMinHeight(root.getPrefHeight());
            primaryStage.setTitle("Visualisation des données");

            // set the controller
            this.mainController = loader.getController();
            this.mainController.setDialogueController(this);
            this.mainController.init();

            // Launch the JSONReader Thread
            JSONReader.getInstance().start();

            // Load Config to the view
            Config.getInstance().setDialogueController(this);
            Config.getInstance().loadConfig();
            
            // show the view
            primaryStage.show();
        } catch (Exception e) {
            e.printStackTrace();
            System.exit(1);
        }
    }

    @Override
    public void init() throws Exception {
        super.init();
        // initialize the JSONController
        JSONWriter.getInstance().init();
        JSONReader.getInstance().init(this);
    }

    @Override
    public void stop() throws Exception {
        super.stop();
        JSONReader.getInstance().stop();
    }

    /**
     * Start the application
     */
    public static void runApp() {
        Application.launch();
    }

    /**
     * CheckBoxListener
     * @param pfKey the key of the data
     * @param pfValue the new value of the data
     */
    public void checkBoxListener(String pfKey, boolean pfValue) {
        JSONWriter.getInstance().updateDonnees(pfKey, pfValue);
    }

    /**
     * SpinnerListener
     * @param key the key of the data
     * @param value the new value of the data
     */
    public void spinnerListener(String key, String value) {
        JSONWriter.getInstance().updateSeuil(key, value);
    }

    /**
     * Send the data to the mainController
     * @param pfData the data to send
     */
    public void sendData(JSONObject pfData) {
        this.mainController.showData(pfData);
    }

    /**
     * Send the config to the mainController
     * @param pfConfig the config to send
     */
    public void loadView(JSONObject pfConfigView) {
        this.mainController.loadView(pfConfigView);
    }

}