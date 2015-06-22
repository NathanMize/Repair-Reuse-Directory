package com.mycompany.foo;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.view.View;
import android.widget.TextView;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;


/**
 * Created by Mollie on 6/9/2015.
 */
public class IndividualBusinessActivity extends Activity {
    TextView nametxt;
    TextView addresstxt;
    TextView phonetxt;
    TextView hourstxt;
    TextView websitetxt;
    TextView infotxt;
    TextView rrtxt;
    String website;
    public final static String EXTRA_MESSAGE = "com.mycompany.foo.MESSAGE";
    public final static String EXTRA_MESSAGE_TWO = "com.mycompany.foo.MESSAGE";

    String message;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_individual_business);


        // Get the message from the intent
        Intent intent = getIntent();
        message = intent.getStringExtra(BusinessActivity.IND_BUSINESS_MESSAGE);

        if(message != null) {
            try {

                JSONObject jsonObj = new JSONObject(message);

                String name = jsonObj.getString("name");
                String address = jsonObj.getString("address");
                String phone = jsonObj.getString("phone");
                String hours = jsonObj.getString("hours");
                website = jsonObj.getString("website");
                String info = jsonObj.getString("info");
                String rr = jsonObj.getString("rr");

                nametxt = (TextView) findViewById(R.id.textView);
                addresstxt = (TextView) findViewById(R.id.textView7);
                phonetxt = (TextView) findViewById(R.id.textView8);
                hourstxt = (TextView) findViewById(R.id.textView9);
                websitetxt = (TextView) findViewById(R.id.textView10);
                infotxt = (TextView) findViewById(R.id.textView11);
                rrtxt = (TextView) findViewById(R.id.textView12);

                nametxt.setText(name);
                addresstxt.setText(address);
                phonetxt.setText(phone);
                hourstxt.setText(hours);
                websitetxt.setText(website);
                infotxt.setText(info);
                rrtxt.setText(rr);

            } catch (JSONException e) {
                e.printStackTrace();
            }
        }

    }
    public void getWebsite(View view) {
        // Do something in response to button
        Intent browserIntent = new Intent(Intent.ACTION_VIEW, Uri.parse(website));
        startActivity(browserIntent);
    }
    /** Called when the user clicks the Send button */
    public void sendMessage(View view) {
        // Do something in response to button
        Intent intent = new Intent(this, MapsActivity.class);
        Bundle extras = new Bundle();
        TextView editText = (TextView) findViewById(R.id.textView7);
        TextView txt = (TextView) findViewById(R.id.textView);
        String addr = editText.getText().toString();
        String name = txt.getText().toString();
        extras.putString("street", addr);
        extras.putString("company",name);
        intent.putExtras(extras);
        startActivity(intent);
    }
}
